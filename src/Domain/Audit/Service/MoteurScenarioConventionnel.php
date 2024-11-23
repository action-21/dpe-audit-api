<?php

namespace App\Domain\Audit\Service;

use App\Domain\Audit\Audit;
use App\Domain\Audit\Data\{SollicitationExterieureCollection, SollicitationExterieureRepository, TbaseRepository};
use App\Domain\Audit\Enum\TypeBatiment;
use App\Domain\Audit\ValueObject\{Occupation, Situation};
use App\Domain\Common\Enum\ZoneClimatique;
use App\Domain\Simulation\Simulation;

final class MoteurScenarioConventionnel
{
    public function __construct(
        private SollicitationExterieureRepository $sollicitation_exterieure_repository,
        private TbaseRepository $tbase_repository,
    ) {}

    public function calcule_situation(Audit $entity, Simulation $simulation): Situation
    {
        $inertie = $simulation->enveloppe()->inertie();

        $tbase = $this->tbase(
            zone_climatique: $entity->zone_climatique(),
            altitude: $entity->altitude()
        );
        $sollicitations_exterieures = $this->sollicitations_exterieures(
            zone_climatique: $entity->zone_climatique(),
            altitude: $entity->altitude(),
            parois_anciennes_lourdes: $inertie->paroi_ancienne && $inertie->inertie->est_lourd(),
        );

        return Situation::create(
            tbase: $tbase,
            sollicitations_exterieures: $sollicitations_exterieures
        );
    }

    public function calcule_occupation(Audit $entity): Occupation
    {
        $nmax = $this->nmax(
            type_batiment: $entity->type_batiment(),
            surface_habitable_batiment: $entity->surface_habitable_reference(),
            nombre_logements: $entity->nombre_logements(),
        );
        $nadeq = $this->nadeq(
            nmax: $nmax,
            nombre_logements: $entity->nombre_logements()
        );

        return Occupation::create(nmax: $nmax, nadeq: $nadeq,);
    }

    public function tbase(ZoneClimatique $zone_climatique, int $altitude): float
    {
        if (null === $valeur = $this->tbase_repository->find_by(
            zone_climatique: $zone_climatique,
            altitude: $altitude,
        )) throw new \DomainException("Valeur forfaitaire Tbase non trouvée");

        return $valeur->tbase;
    }

    public function sollicitations_exterieures(
        ZoneClimatique $zone_climatique,
        int $altitude,
        bool $parois_anciennes_lourdes,
    ): SollicitationExterieureCollection {
        $collection = $this->sollicitation_exterieure_repository->search_by(
            zone_climatique: $zone_climatique,
            altitude: $altitude,
            parois_anciennes_lourdes: $parois_anciennes_lourdes,
        );

        if (false === $collection->est_valide())
            throw new \DomainException("Sollicitations extérieures non trouvées");

        return $collection;
    }

    public function nmax(
        TypeBatiment $type_batiment,
        float $surface_habitable_batiment,
        int $nombre_logements,
    ): float {
        $surface_moyenne = $surface_habitable_batiment / $nombre_logements;

        return match ($type_batiment) {
            TypeBatiment::MAISON => match (true) {
                $surface_moyenne < 30 => 1,
                $surface_moyenne < 70 => 1.75 - 0.01875 * (70 - $surface_moyenne),
                default => 0.025 * $surface_moyenne,
            },
            TypeBatiment::IMMEUBLE => match (true) {
                $surface_moyenne < 10 => 1,
                $surface_moyenne < 50 => 1.75 - 0.01875 * (50 - $surface_moyenne),
                default => 0.035 * $surface_moyenne,
            },
        };
    }

    public function nadeq(float $nmax, int $nombre_logements): float
    {
        return $nmax < 1.75 ? $nombre_logements * $nmax : $nombre_logements * (1.75 + 0.3 * ($nmax - 1.75));
    }
}
