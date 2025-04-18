<?php

namespace App\Domain\Audit\Engine;

use App\Domain\Audit\Audit;
use App\Domain\Audit\Enum\Etiquette;
use App\Domain\Audit\Service\AuditTableValeurRepository;
use App\Domain\Audit\ValueObject\{Consommation, Consommations};
use App\Domain\Chauffage\Engine\Consommation\ConsommationChauffage;
use App\Domain\Common\EngineRule;
use App\Domain\Common\Enum\{ScenarioUsage, Usage};
use App\Domain\Eclairage\Engine\ConsommationEclairage;
use App\Domain\Ecs\Engine\Consommation\ConsommationEcs;
use App\Domain\Refroidissement\Engine\ConsommationRefroidissement;
use App\Domain\Ventilation\Engine\ConsommationVentilation;

final class PerformanceEnergetique extends EngineRule
{
    private Audit $audit;

    public function __construct(private readonly AuditTableValeurRepository $table_repository) {}

    /**
     * @see \App\Domain\Audit\Engine\ZoneThermique::surface_habitable()
     */
    public function surface_habitable(): float
    {
        return $this->audit->data()->surface_habitable;
    }

    /**
     * @see \App\Domain\Audit\Engine\PerformanceClimatique::eges()
     */
    public function eges(ScenarioUsage $scenario): float
    {
        return $this->audit->data()->emissions->get($scenario);
    }

    /**
     * @see \App\Domain\Chauffage\Engine\Consommation\ConsommationChauffage::cch()
     */
    public function cch(ScenarioUsage $scenario, ?bool $energie_primaire = false): float
    {
        return $this->audit->chauffage()->data()->consommations->get(
            scenario: $scenario,
            usage: Usage::CHAUFFAGE,
            energie_primaire: $energie_primaire
        );
    }

    /**
     * @see \App\Domain\Ecs\Engine\Consommation\ConsommationEcs::cecs()
     */
    public function cecs(ScenarioUsage $scenario, ?bool $energie_primaire = false): float
    {
        return $this->audit->ecs()->data()->consommations->get(
            scenario: $scenario,
            usage: Usage::ECS,
            energie_primaire: $energie_primaire
        );
    }

    /**
     * @see \App\Domain\Refroidissement\Engine\ConsommationRefroidissement::cfr()
     */
    public function cfr(ScenarioUsage $scenario, ?bool $energie_primaire = false): float
    {
        return $this->audit->refroidissement()->data()->consommations->get(
            scenario: $scenario,
            usage: Usage::REFROIDISSEMENT,
            energie_primaire: $energie_primaire
        );
    }

    /**
     * @see \App\Domain\Eclairage\Engine\ConsommationEclairage::cecl()
     */
    public function cecl(ScenarioUsage $scenario, ?bool $energie_primaire = false): float
    {
        return $this->audit->eclairage()->data()->consommations->get(
            scenario: $scenario,
            usage: Usage::ECLAIRAGE,
            energie_primaire: $energie_primaire
        );
    }

    /**
     * @see \App\Domain\Chauffage\Engine\Consommation\ConsommationAuxiliaire::caux()
     * @see \App\Domain\Ecs\Engine\Consommation\ConsommationAuxiliaire::caux()
     * @see \App\Domain\Ventilation\Engine\ConsommationVentilation::caux()
     */
    public function caux(ScenarioUsage $scenario, ?bool $energie_primaire = false): float
    {
        $caux = $this->audit->chauffage()->data()->consommations->get(
            scenario: $scenario,
            usage: Usage::AUXILIAIRE,
            energie_primaire: $energie_primaire
        );
        $caux += $this->audit->ecs()->data()->consommations->get(
            scenario: $scenario,
            usage: Usage::AUXILIAIRE,
            energie_primaire: $energie_primaire
        );
        $caux += $this->audit->ventilation()->data()->consommations->get(
            scenario: $scenario,
            usage: Usage::AUXILIAIRE,
            energie_primaire: $energie_primaire
        );
        return $caux;
    }

    /**
     * Consommation annuelle d'énergie finale exprimée en kWh
     */
    public function cef(ScenarioUsage $scenario): float
    {
        return array_sum([
            $this->cch($scenario),
            $this->cecs($scenario),
            $this->cfr($scenario),
            $this->cecl($scenario),
            $this->caux($scenario),
        ]) / $this->surface_habitable();
    }

    /**
     * Consommation annuelle d'énergie primaire exprimée en kWh
     */
    public function cep(ScenarioUsage $scenario): float
    {
        return array_sum([
            $this->cch($scenario, true),
            $this->cecs($scenario, true),
            $this->cfr($scenario, true),
            $this->cecl($scenario, true),
            $this->caux($scenario, true),
        ]) / $this->surface_habitable();
    }

    /**
     * Etiquette énergie
     */
    public function etiquette_energie(): Etiquette
    {
        if (null === $etiquette = $this->table_repository->etiquette_energie(
            zone_climatique: $this->audit->adresse()->zone_climatique,
            altitude: $this->audit->batiment()->altitude,
            cep: $this->cep(ScenarioUsage::CONVENTIONNEL),
            eges: $this->eges(ScenarioUsage::CONVENTIONNEL),
        )) {
            throw new \DomainException("Etiquette énergie non trouvée");
        }
        return $etiquette;
    }

    public function apply(Audit $entity): void
    {
        $this->audit = $entity;

        $consommations = ScenarioUsage::each(fn(ScenarioUsage $scenario) => Consommation::create(
            scenario: $scenario,
            consommation_ef: $this->cef($scenario),
            consommation_ep: $this->cep($scenario),
        ));

        $entity->calcule($entity->data()->with(
            consommations: Consommations::from(...$consommations),
            etiquette_energie: $this->etiquette_energie(),
        ));
    }

    public static function dependencies(): array
    {
        return [
            PerformanceClimatique::class,
            ZoneThermique::class,
            ConsommationChauffage::class,
            ConsommationEclairage::class,
            ConsommationEcs::class,
            ConsommationRefroidissement::class,
            ConsommationVentilation::class,
        ];
    }
}
