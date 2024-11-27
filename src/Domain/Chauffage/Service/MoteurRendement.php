<?php

namespace App\Domain\Chauffage\Service;

use App\Domain\Chauffage\Data\{FchRepository, I0Repository};
use App\Domain\Chauffage\Entity\{Emetteur, Systeme};
use App\Domain\Chauffage\Enum\{TypeEmission, TypeIntermittence};
use App\Domain\Chauffage\ValueObject\{Rendement, RendementCollection};
use App\Domain\Common\Enum\{Enum, ScenarioUsage, ZoneClimatique};
use App\Domain\Simulation\Simulation;

final class MoteurRendement
{
    public function __construct(
        private MoteurDimensionnement $moteur_dimensionnement,
        private MoteurRendement\MoteurRendementDistribution $moteur_rendement_distribution,
        private MoteurRendement\MoteurRendementEmission $moteur_rendement_emission,
        private MoteurRendement\MoteurRendementGeneration $moteur_rendement_generation,
        private MoteurRendement\MoteurRendementRegulation $moteur_rendement_regulation,
        private FchRepository $fch_repository,
        private I0Repository $i0_repository,
    ) {}

    public function calcule_rendement(Systeme $entity, Simulation $simulation): RendementCollection
    {
        $collection = [];
        $int = $this->calcule_int($entity, $simulation, $i0);
        $fch = $this->calcule_fch($entity);

        foreach (ScenarioUsage::cases() as $scenario) {
            $ich = $this->calcule_ich(entity: $entity, simulation: $simulation, scenario: $scenario, rg: $rg, re: $re, rd: $rd, rr: $rr);
            $collection[] = Rendement::create(scenario: $scenario, fch: $fch, i0: $i0, int: $int, ich: $ich, rg: $rg, rd: $rd, re: $re, rr: $rr,);
        }
        return new RendementCollection($collection);
    }

    private function calcule_fch(Systeme $entity): float
    {
        return $this->fch(
            type_batiment: $entity->chauffage()->audit()->type_batiment(),
            zone_climatique: $entity->chauffage()->audit()->zone_climatique(),
            chauffage_solaire: $entity->installation()->solaire() !== null,
            fch_saisi: $entity->installation()->solaire()?->fch,
        );
    }

    public function calcule_ich(Systeme $entity, Simulation $simulation, ScenarioUsage $scenario, ?float &$rg, ?float &$rd, ?float &$re, ?float &$rr,): float
    {
        $rg = $this->moteur_rendement_generation->calcule_rendement_generation($entity, $simulation, $scenario);
        $rd = $this->moteur_rendement_distribution->calcule_rendement_distribution($entity);
        $re = $this->moteur_rendement_emission->calcule_rendement_emission($entity);
        $rr = $this->moteur_rendement_regulation->calcule_rendement_regulation($entity);
        return $this->ich(rg: $rg, rd: $rd, re: $re, rr: $rr);
    }

    public function calcule_int(Systeme $entity, Simulation $simulation, ?float &$i0 = null): float
    {
        $gv = $simulation->enveloppe()->performance()->gv;
        $i0 = $this->calcule_i0($entity, $simulation);
        $g = $this->g(
            gv: $gv,
            hauteur_sous_plafond: $entity->chauffage()->audit()->hauteur_sous_plafond_reference(),
            surface_habitable: $entity->chauffage()->audit()->surface_habitable_reference(),
        );
        return $this->int(i0: $i0, g: $g);
    }

    public function calcule_i0(Systeme $entity, Simulation $simulation): float
    {
        $inertie = $simulation->enveloppe()->inertie();

        $type_intermittence = TypeIntermittence::determine(
            regulation_centrale: $entity->installation()->regulation_centrale(),
            regulation_terminale: $entity->installation()->regulation_terminale(),
            chauffage_collectif: $entity->generateur()->generateur_collectif(),
        );

        if ($entity->emetteurs()->count() === 0) {
            return $this->i0(
                type_batiment: $entity->chauffage()->audit()->type_batiment(),
                type_emission: TypeEmission::from_type_generateur($entity->generateur()->type()),
                type_intermittence: $type_intermittence,
                regulation_terminale: $entity->installation()->regulation_terminale()->presence_regulation,
                inertie_lourde: $inertie->inertie->est_lourd(),
                comptage_individuel: $entity->installation()->comptage_individuel(),
                chauffage_collectif: $entity->generateur()->generateur_collectif(),
                chauffage_central: false,
            );
        }
        return \array_reduce($entity->emetteurs()->values(), fn(float $carry, Emetteur $item) => $carry += $this->i0(
            type_batiment: $item->chauffage()->audit()->type_batiment(),
            type_emission: $item->type_emission(),
            type_intermittence: $type_intermittence,
            regulation_terminale: $entity->installation()->regulation_terminale()->presence_regulation,
            inertie_lourde: $inertie->inertie->est_lourd(),
            comptage_individuel: $entity->installation()->comptage_individuel(),
            chauffage_collectif: $entity->generateur()->generateur_collectif(),
            chauffage_central: true,
        ), 0) / $entity->emetteurs()->count();
    }

    public function ich(float $rg, float $rd, float $re, float $rr): float
    {
        return 1 / ($rg * $rd * $re * $rr);
    }

    /**
     * Facteur d'intermittence
     * 
     * @param float $i0 - Coefficient d'intermittence
     * @param float $g - Déperditions annuelles de l'enveloppe ((W/K)/m3)
     */
    public function int(float $i0, float $g): float
    {
        return $i0 / (1 + 0.1 * ($g - 1));
    }

    /**
     * Déperditions annuelles de l'enveloppe ((W/K)/m3)
     * 
     * @param float $gv - Déperditions annuelles de l'enveloppe (W/K)
     */
    public function g(float $gv, float $hauteur_sous_plafond, float $surface_habitable): float
    {
        return ($hauteur_sous_plafond > 0 && $surface_habitable > 0)
            ? $gv / ($hauteur_sous_plafond * $surface_habitable)
            : 0;
    }

    /**
     * Facteur de couverture solaire
     */
    public function fch(
        Enum $type_batiment,
        ZoneClimatique $zone_climatique,
        bool $chauffage_solaire,
        ?float $fch_saisi,
    ): float {
        if (false === $chauffage_solaire)
            return 0;
        if ($fch_saisi)
            return $fch_saisi;
        if (null === $data = $this->fch_repository->find_by(
            type_batiment: $type_batiment,
            zone_climatique: $zone_climatique,
        )) throw new \DomainException("Valeur forfaitaire Fch non trouvée.");

        return $data->fch;
    }

    /**
     * Coefficient d'intermittence
     */
    public function i0(
        Enum $type_batiment,
        TypeEmission $type_emission,
        TypeIntermittence $type_intermittence,
        bool $chauffage_central,
        bool $regulation_terminale,
        bool $chauffage_collectif,
        bool $inertie_lourde,
        ?bool $comptage_individuel,
    ): float {
        if (null === $data = $this->i0_repository->find_by(
            type_batiment: $type_batiment,
            type_emission: $type_emission,
            type_intermittence: $type_intermittence,
            chauffage_central: $chauffage_central,
            regulation_terminale: $regulation_terminale,
            chauffage_collectif: $chauffage_collectif,
            inertie_lourde: $inertie_lourde,
            comptage_individuel: $comptage_individuel,
        )) throw new \DomainException("Valeur forfaitaire I0 non trouvée.");

        return $data->i0;
    }
}
