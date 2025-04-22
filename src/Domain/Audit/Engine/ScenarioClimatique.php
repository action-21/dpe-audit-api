<?php

namespace App\Domain\Audit\Engine;

use App\Domain\Audit\Audit;
use App\Domain\Audit\Service\AuditTableValeurRepository;
use App\Domain\Audit\ValueObject\SollicitationsExterieures;
use App\Domain\Common\EngineRule;
use App\Domain\Enveloppe\Engine\Inertie\InertieEnveloppe;
use App\Domain\Enveloppe\Enum\Inertie;

final class ScenarioClimatique extends EngineRule
{
    private Audit $audit;

    public function __construct(private readonly AuditTableValeurRepository $table_repository,) {}

    public function parois_anciennes_lourdes(): bool
    {
        $inertie = $this->audit->enveloppe()->data()->inertie;
        $materiaux_anciens = $this->audit->batiment()->materiaux_anciens;

        return $materiaux_anciens && \in_array($inertie, [
            Inertie::TRES_LOURDE,
            Inertie::LOURDE,
        ]);
    }

    /**
     * Température extérieure de base exprimée en °C
     */
    public function tbase(): float
    {
        return $this->get("tbase", function () {
            if (null === $tbase = $this->table_repository->tbase(
                zone_climatique: $this->audit->adresse()->zone_climatique,
                altitude: $this->audit->batiment()->altitude,
            )) {
                throw new \DomainException("Valeur forfaitaire Tbase non trouvée");
            }
            return $tbase;
        });
    }

    /**
     * Sollicitations extérieures pour chaque mois de l'année
     */
    public function sollicitations_exterieures(): SollicitationsExterieures
    {
        return $this->get("sollicitations_exterieures", function () {
            if (null === $sollicitations_exterieures = $this->table_repository->sollicitations_exterieures(
                zone_climatique: $this->audit->adresse()->zone_climatique,
                altitude: $this->audit->batiment()->altitude,
                parois_anciennes_lourdes: $this->parois_anciennes_lourdes(),
            )) {
                throw new \DomainException("Valeur forfaitaire Tbase non trouvée");
            }
            return $sollicitations_exterieures;
        });
    }

    public function apply(Audit $entity): void
    {
        $this->audit = $entity;
        $this->clear();

        $entity->calcule($entity->data()->with(
            sollicitations_exterieures: $this->sollicitations_exterieures(),
            tbase: $this->tbase(),
        ));
    }

    public static function dependencies(): array
    {
        return [
            InertieEnveloppe::class,
        ];
    }
}
