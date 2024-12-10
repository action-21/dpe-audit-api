<?php

namespace App\Domain\Chauffage\Service\MoteurRendement;

use App\Domain\Chauffage\Data\RgRepository;
use App\Domain\Chauffage\Entity\Systeme;
use App\Domain\Chauffage\Enum\{EnergieGenerateur, LabelGenerateur, TypeGenerateur};
use App\Domain\Common\Enum\{ScenarioUsage, ZoneClimatique};
use App\Domain\Simulation\Simulation;

final class MoteurRendementGeneration
{
    public function __construct(
        private MoteurRendementGenerationCombustion $moteur_rendement_generation_combustion,
        private RgRepository $rg_repository,
    ) {}

    public function calcule_rendement_generation(Systeme $entity, Simulation $simulation, ScenarioUsage $scenario): ?float
    {
        if ($entity->generateur()->performance()->scop)
            return $entity->generateur()->performance()->scop;
        if ($rg = $this->calcule_rendement_generation_combustion($entity, $simulation, $scenario))
            return $rg;
        if ($rg = $this->calcule_rendement_generation_hybride($entity, $simulation, $scenario))
            return $rg;

        return $this->rg(
            type_generateur: $entity->generateur()->type(),
            energie_generateur: $entity->generateur()->energie(),
            label_generateur: $entity->generateur()->signaletique()?->label,
            annee_installation_generateur: $entity->generateur()->annee_installation() ?? $entity->chauffage()->annee_construction_batiment(),
        );
    }

    public function calcule_rendement_generation_combustion(Systeme $entity, Simulation $simulation, ScenarioUsage $scenario): ?float
    {
        if (false === $entity->generateur()->combustion())
            return null;

        $service = ($this->moteur_rendement_generation_combustion)($entity, $simulation, $scenario);
        return $service->calcule_rendement_generation();
    }

    public function calcule_rendement_generation_hybride(Systeme $entity, Simulation $simulation, ScenarioUsage $scenario): ?float
    {
        if (false === $this->rg_pac_hybride_applicable($entity->generateur()->type()))
            return null;

        return $this->rg_pac_hybride(
            zone_climatique: $entity->chauffage()->audit()->zone_climatique(),
            rg_chaudiere: $this->calcule_rendement_generation_combustion($entity, $simulation, $scenario),
            rg_pac: $entity->generateur()->performance()->scop,
        );
    }

    public function rg(
        TypeGenerateur $type_generateur,
        EnergieGenerateur $energie_generateur,
        ?LabelGenerateur $label_generateur,
        int $annee_installation_generateur,
    ): float {
        if (null === $data = $this->rg_repository->find_by(
            type_generateur: $type_generateur,
            energie_generateur: $energie_generateur,
            label_generateur: $label_generateur,
            annee_installation_generateur: $annee_installation_generateur,
        )) throw new \DomainException("Valeur forfaitaire Rg non trouvée.");

        return $data->rg;
    }

    public function rg_applicable(TypeGenerateur $type_generateur, EnergieGenerateur $energie_generateur,): bool
    {
        if ($type_generateur->is_chauffage_electrique() || $type_generateur->is_poele_insert()) {
            return true;
        }
        if (\in_array($type_generateur, [
            TypeGenerateur::RESEAU_CHALEUR,
            TypeGenerateur::CHAUDIERE_MULTI_BATIMENT,
            TypeGenerateur::PAC_MULTI_BATIMENT,
        ])) {
            return true;
        }
        return \in_array($type_generateur, [TypeGenerateur::CHAUDIERE]) && $energie_generateur === EnergieGenerateur::ELECTRICITE;
    }

    public function rg_pac_hybride(ZoneClimatique $zone_climatique, float $rg_chaudiere, float $rg_pac,): float
    {
        $tx_chaudiere = $this->taux_couverture_hybride_partie_chaudiere(zone_climatique: $zone_climatique);
        $tx_pac = $this->taux_couverture_hybride_partie_pac(zone_climatique: $zone_climatique);
        return $rg_chaudiere * $tx_chaudiere + $rg_pac * $tx_pac;
    }

    public function rg_pac_hybride_applicable(TypeGenerateur $type_generateur,): bool
    {
        return $type_generateur->combustion_applicable() && $type_generateur->scop_applicable();
    }

    public function taux_couverture_hybride_partie_chaudiere(ZoneClimatique $zone_climatique): float
    {
        return match ($zone_climatique->code()) {
            'H1' => 0.2,
            'H2' => 0.17,
            'H3' => 0.12,
        };
    }

    public function taux_couverture_hybride_partie_pac(ZoneClimatique $zone_climatique): float
    {
        return match ($zone_climatique->code()) {
            'H1' => 0.8,
            'H2' => 0.83,
            'H3' => 0.88,
        };
    }
}
