<?php

namespace App\Domain\Enveloppe\Engine\Apport;

use App\Domain\Audit\Audit;
use App\Domain\Common\EngineRule;
use App\Domain\Common\Enum\Orientation;
use App\Domain\Enveloppe\Entity\Baie\MasqueLointain;
use App\Domain\Enveloppe\Enum\Baie\{SecteurChampsVision, TypeMasqueLointain};
use App\Domain\Enveloppe\Service\BaieTableValeurRepository;

final class EnsoleillementMasqueLointain extends EngineRule
{
    private MasqueLointain $masque;

    public function __construct(private readonly BaieTableValeurRepository $table_repository) {}

    public function secteur(): ?SecteurChampsVision
    {
        if ($this->masque->type_masque() !== TypeMasqueLointain::MASQUE_LOINTAIN_NON_HOMOGENE) {
            return null;
        }
        if (null === $this->masque->baie()->orientation()) {
            return null;
        }
        $orientation_masque = $this->masque->orientation()->value;
        $orientation_baie = $this->masque->baie()->orientation()->value;
        $diff = \abs($orientation_masque - $orientation_baie);

        return match (Orientation::from_azimut($orientation_baie)) {
            Orientation::NORD => match (true) {
                $diff >= -90 && $diff < -45 => SecteurChampsVision::SECTEUR_LATERAL_OUEST,
                $diff >= -45 && $diff <= 0 => SecteurChampsVision::SECTEUR_CENTRAL_OUEST,
                $diff >= 0 && $diff <= 45 => SecteurChampsVision::SECTEUR_CENTRAL_EST,
                $diff > 45 && $diff <= 90 => SecteurChampsVision::SECTEUR_LATERAL_EST,
            },
            Orientation::EST => match (true) {
                $diff >= -90 && $diff < -45 => SecteurChampsVision::SECTEUR_LATERAL_NORD,
                $diff >= -45 && $diff < 0 => SecteurChampsVision::SECTEUR_CENTRAL_NORD,
                $diff >= 0 && $diff <= 45 => SecteurChampsVision::SECTEUR_CENTRAL_SUD,
                $diff > 45 && $diff <= 90 => SecteurChampsVision::SECTEUR_LATERAL_SUD,
            },
            Orientation::SUD => match (true) {
                $diff >= -90 && $diff < -45 => SecteurChampsVision::SECTEUR_LATERAL_EST,
                $diff >= -45 && $diff <= 0 => SecteurChampsVision::SECTEUR_CENTRAL_EST,
                $diff >= 0 && $diff <= 45 => SecteurChampsVision::SECTEUR_CENTRAL_OUEST,
                $diff > 45 && $diff <= 90 => SecteurChampsVision::SECTEUR_LATERAL_OUEST,
            },
            Orientation::OUEST => match (true) {
                $diff >= -90 && $diff < -45 => SecteurChampsVision::SECTEUR_LATERAL_SUD,
                $diff >= -45 && $diff <= 0 => SecteurChampsVision::SECTEUR_CENTRAL_SUD,
                $diff > 0 && $diff <= 45 => SecteurChampsVision::SECTEUR_CENTRAL_NORD,
                $diff > 45 && $diff <= 90 => SecteurChampsVision::SECTEUR_LATERAL_NORD,
            }
        };
    }

    /**
     * Facteur d'ensoleillement
     */
    public function fe2(): float
    {
        return $this->get("fe2", function () {
            if ($this->masque->type_masque() !== TypeMasqueLointain::MASQUE_LOINTAIN_HOMOGENE) {
                return 1;
            }
            if (null === $fe2 = $this->table_repository->fe2(
                type_masque_lointain: $this->masque->type_masque(),
                orientation: $this->masque->baie()->position()->orientation?->enum(),
                hauteur_masque_alpha: $this->masque->hauteur(),
            )) {
                throw new \DomainException('Valeur forfaitaire fe2 non trouvée');
            }
            return $fe2;
        });
    }

    /**
     * Facteur d'ombrage
     */
    public function omb(): float
    {
        return $this->get("omb", function () {
            if ($this->masque->type_masque() !== TypeMasqueLointain::MASQUE_LOINTAIN_NON_HOMOGENE) {
                return 0;
            }
            if (null === $omb = $this->table_repository->omb(
                type_masque_lointain: $this->masque->type_masque(),
                secteur: $this->secteur(),
                orientation: $this->masque->baie()->position()->orientation?->enum(),
                hauteur_masque_alpha: $this->masque->hauteur(),
            )) {
                throw new \DomainException('Valeur forfaitaire omb non trouvée');
            }
            return $omb;
        });
    }

    public function apply(Audit $entity): void
    {
        foreach ($entity->enveloppe()->baies() as $baie) {
            foreach ($baie->masques_lointains() as $masque) {
                $this->masque = $masque;
                $this->clear();

                $masque->calcule($masque->data()->with(
                    fe2: $this->fe2(),
                    omb: $this->omb(),
                ));
            }
        }
    }
}
