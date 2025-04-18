<?php

namespace App\Domain\Enveloppe\Engine\Apport;

use App\Domain\Audit\Audit;
use App\Domain\Common\EngineRule;
use App\Domain\Common\Enum\Mois;
use App\Domain\Enveloppe\Entity\Lnc\Baie;
use App\Domain\Enveloppe\ValueObject\Lnc\{EnsoleillementBaie as Ensoleillement, EnsoleillementsBaie as Ensoleillements};
use App\Domain\Enveloppe\Service\LncTableValeurRepository;

final class EnsoleillementBaieLnc extends EngineRule
{
    private Audit $audit;
    private Baie $baie;

    public function __construct(
        private readonly LncTableValeurRepository $table_repository,
    ) {}

    /**
     * Facteur d'ensoleillement
     */
    public function fe(): float
    {
        return 1;
    }

    /**
     * Coefficient de transparence
     */
    public function t(): float
    {
        if (null === $value = $this->table_repository->t(
            type_baie: $this->baie->type(),
            materiau: $this->baie->materiau(),
            presence_rupteur_pont_thermique: $this->baie->presence_rupteur_pont_thermique(),
            type_vitrage: $this->baie->type_vitrage(),
        )) {
            throw new \DomainException('Valeur forfaitaire t non trouvée');
        }
        return $value;
    }

    /**
     * Coefficient d'orientation et d'inclinaison
     */
    public function c1(Mois $mois): float
    {
        if (null === $value = $this->table_repository->c1(
            mois: $mois,
            zone_climatique: $this->audit->adresse()->zone_climatique,
            inclinaison: $this->baie->position()->inclinaison,
            orientation: $this->baie->position()->orientation,
        )) {
            throw new \DomainException('Valeur forfaitaire t non trouvée');
        }
        return $value;
    }

    /**
     * Surface sud équivalente des apports totaux dans la véranda par la baie exprimée en m²
     */
    public function sst(Mois $mois): float
    {
        $surface = $this->baie->position()->surface;
        $t = $this->t();
        $c1 = $this->c1(mois: $mois);
        $fe = $this->fe();

        return $surface * (0.8 * $t + 0.024) * $fe * $c1;
    }

    public function apply(Audit $entity): void
    {
        $this->audit = $entity;

        foreach ($entity->enveloppe()->locaux_non_chauffes() as $lnc) {
            foreach ($lnc->baies() as $baie) {
                $this->baie = $baie;

                $ensoleillements = [];

                foreach (Mois::cases() as $mois) {
                    $ensoleillements[] = Ensoleillement::create(
                        mois: $mois,
                        sst: $this->sst(mois: $mois),
                        fe: $this->fe(),
                        c1: $this->c1(mois: $mois),
                        t: $this->t(),
                    );
                }

                $baie->calcule($baie->data()->with(
                    ensoleillements: Ensoleillements::create(...$ensoleillements)
                ));
            }
        }
    }
}
