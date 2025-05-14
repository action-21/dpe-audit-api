<?php

namespace App\Engine\Performance\Scenario;

use App\Domain\Audit\Audit;
use App\Domain\Audit\Service\AuditTableValeurRepository;
use App\Domain\Audit\ValueObject\SollicitationsExterieures;
use App\Domain\Common\Enum\ZoneClimatique;
use App\Domain\Enveloppe\Enum\Inertie;
use App\Engine\Performance\Inertie\InertieEnveloppe;
use App\Engine\Performance\Rule;

final class ScenarioClimatique extends Rule
{
    private Audit $audit;

    public function __construct(private readonly AuditTableValeurRepository $table_repository,) {}

    public function parois_anciennes_lourdes(): bool
    {
        return $this->get("parois_anciennes_lourdes", function () {
            $inertie = $this->audit->enveloppe()->data()->inertie;
            $materiaux_anciens = $this->audit->batiment()->materiaux_anciens;

            return $materiaux_anciens && \in_array($inertie, [
                Inertie::TRES_LOURDE,
                Inertie::LOURDE,
            ]);
        });
    }

    /**
     * Zone climatique de référence
     */
    public function zone_climatique(): ?ZoneClimatique
    {
        return $this->get("zone_climatique", function () {
            return ZoneClimatique::from_code_departement($this->audit->adresse()->code_departement);
        });
    }

    /**
     * Température extérieure de base exprimée en °C
     */
    public function tbase(): float
    {
        return $this->get("tbase", function () {
            if (null === $tbase = $this->table_repository->tbase(
                zone_climatique: $this->zone_climatique(),
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
                zone_climatique: $this->zone_climatique(),
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
            zone_climatique: $this->zone_climatique(),
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
