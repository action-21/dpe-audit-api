<?php

namespace App\Domain\Enveloppe\Engine\Apport;

use App\Domain\Audit\Audit;
use App\Domain\Common\EngineRule;
use App\Domain\Common\Enum\Mois;
use App\Domain\Common\ValueObject\Pourcentage;
use App\Domain\Enveloppe\Entity\Baie;
use App\Domain\Enveloppe\Entity\Baie\MasqueLointain;
use App\Domain\Enveloppe\Enum\Baie\{Materiau, TypeMasqueLointain, TypeSurvitrage, TypeVitrage};
use App\Domain\Enveloppe\Service\BaieTableValeurRepository;
use App\Domain\Enveloppe\ValueObject\Baie\{Ensoleillement, Ensoleillements};
use App\Domain\Enveloppe\Engine\Apport\EnsoleillementDoubleFenetre;

final class EnsoleillementBaie extends EngineRule
{
    private Audit $audit;
    private Baie $baie;

    public function __construct(private readonly BaieTableValeurRepository $table_repository) {}

    public function materiau(): Materiau
    {
        return $this->baie->materiau() ?? Materiau::PVC;
    }

    public function type_vitrage(): ?TypeVitrage
    {
        if ($this->baie->vitrage()?->type_vitrage) {
            return $this->baie->vitrage()->type_vitrage;
        }
        if (null !== $this->baie->type_baie()->is_paroi_vitree()) {
            return TypeVitrage::SIMPLE_VITRAGE;
        }
        return null;
    }

    public function type_survitrage(): ?TypeSurvitrage
    {
        if (null === $this->baie->vitrage()?->survitrage) {
            return null;
        }
        if ($this->baie->vitrage()?->survitrage?->type_survitrage) {
            return $this->baie->vitrage()->survitrage->type_survitrage;
        }
        if (null !== $this->baie->type_baie()->is_paroi_vitree()) {
            return TypeSurvitrage::SURVITRAGE_SIMPLE;
        }
        return null;
    }

    /**
     * Facteur d'ensoleillement
     */
    public function fe(): float
    {
        return $this->fe1() * $this->fe2();
    }

    /**
     * Facteur d'ensoleillement dû aux masques proches
     */
    public function fe1(): float
    {
        return $this->get('fe1', function () {
            $fe1 = 1;

            foreach ($this->baie->masques_proches() as $masque) {
                if ($masque->data()->fe1 < $fe1) {
                    $fe1 = $masque->data()->fe1;
                }
            }
            return $fe1;
        });
    }

    /**
     * Facteur d'ensoleillement dû aux masques lointains
     */
    public function fe2(): float
    {
        return $this->get('fe2', function () {
            $fe2 = 1;

            foreach ($this->baie->masques_lointains()->with_type(TypeMasqueLointain::MASQUE_LOINTAIN_HOMOGENE) as $masque) {
                if ($masque->data()->fe2 < $fe2) {
                    $fe2 = $masque->data()->fe2;
                }
            }

            $omb = (100 - $this->omb()) / 100;

            return min($fe2, $omb);
        });
    }

    /**
     * Facteur d'ombrage
     */
    public function omb(): float
    {
        return $this->get('omb', function () {
            $omb = $this->baie->masques_lointains()
                ->with_type(TypeMasqueLointain::MASQUE_LOINTAIN_NON_HOMOGENE)
                ->reduce(fn(float $omb, MasqueLointain $masque): float => $omb + $masque->data()->omb);

            return min($omb, 100);
        });
    }

    /**
     * Proportion d'énergie solaire
     */
    public function sw(): Pourcentage
    {
        return $this->get('sw', function () {
            return $this->baie->double_fenetre()
                ? Pourcentage::from($this->sw1()->decimal() * $this->baie->double_fenetre()->data()->sw->decimal())
                : $this->sw1();
        });
    }

    /**
     * Proportion d'énergie solaire
     */
    public function sw1(): Pourcentage
    {
        return $this->get('sw1', function () {
            if ($this->baie->performance()->sw) {
                return $this->baie->performance()->sw;
            }
            if (null === $sw = $this->table_repository->sw(
                type_baie: $this->baie->type_baie(),
                type_pose: $this->baie->type_pose(),
                presence_soubassement: $this->baie->presence_soubassement(),
                materiau: $this->materiau(),
                type_vitrage: $this->type_vitrage(),
                type_survitrage: $this->type_survitrage(),
            )) {
                throw new \DomainException('Valeur forfaitaire sw non trouvée');
            }
            return $sw;
        });
    }

    /**
     * Coefficient d'orientation et d'inclinaison
     */
    public function c1(Mois $mois): float
    {
        return $this->get('c1', function () use ($mois) {
            if (null === $c1 = $this->table_repository->c1(
                mois: $mois,
                zone_climatique: $this->audit->adresse()->zone_climatique,
                inclinaison: $this->baie->position()->inclinaison,
                orientation: $this->baie->position()->orientation?->enum(),
            )) {
                throw new \DomainException('Valeur forfaitaire c1 non trouvée');
            }
            return $c1;
        });
    }

    /**
     * Surface sud équivalente exprimée en m²
     */
    public function sse(Mois $mois): float
    {
        return $this->baie->position()->surface * $this->fe() * $this->sw()->decimal() * $this->c1($mois);
    }

    public function apply(Audit $entity): void
    {
        $this->audit = $entity;

        foreach ($entity->enveloppe()->baies() as $baie) {
            $this->baie = $baie;
            $this->clear();

            $ensoleillements = [];

            foreach (Mois::cases() as $mois) {
                $ensoleillements[] = Ensoleillement::create(
                    mois: $mois,
                    fe: $this->fe(),
                    sw: $this->sw(),
                    c1: $this->c1($mois),
                    sse: $this->sse($mois),
                );
            }

            $baie->calcule($baie->data()->with(
                ensoleillements: Ensoleillements::create(...$ensoleillements)
            ));
        }
    }

    public static function dependencies(): array
    {
        return [
            EnsoleillementDoubleFenetre::class,
            EnsoleillementMasqueLointain::class,
            EnsoleillementMasqueProche::class,
        ];
    }
}
