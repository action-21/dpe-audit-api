<?php

namespace App\Engine\Performance\Ecs\Besoin;

use App\Domain\Audit\Audit;
use App\Domain\Audit\Enum\TypeBatiment;
use App\Domain\Common\Enum\{Mois, ScenarioUsage, Usage};
use App\Domain\Common\ValueObject\Besoins;
use App\Engine\Performance\Rule;
use App\Engine\Performance\Scenario\{ScenarioClimatique, ZoneThermique};

final class BesoinEcs extends Rule
{
    private Audit $audit;

    /**
     * @see \App\Engine\Performance\Scenario\ZoneThermique::surface_habitable()
     */
    public function surface_habitable(): float
    {
        return $this->audit->data()->surface_habitable;
    }

    /**
     * @see \App\Engine\Performance\Scenario\ZoneThermique::nombre_logements()
     */
    public function nombre_logements(): float
    {
        return $this->audit->data()->nombre_logements;
    }

    /**
     * @see \App\Engine\Performance\Scenario\ScenarioClimatique::sollicitations_exterieures()
     */
    public function tefs(Mois $mois): float
    {
        return $this->audit->data()->sollicitations_exterieures->tefs(mois: $mois);
    }

    /**
     * Besoin mensuel d'eau chaude sanitaire exprimée en kWh
     */
    public function becs(ScenarioUsage $scenario, Mois $mois): float
    {
        $nj = $mois->nj();
        $nadeq = $this->nadeq();
        $tefs = $this->tefs(mois: $mois);

        return match ($scenario) {
            ScenarioUsage::CONVENTIONNEL => 1.163 * $nadeq * 56 * (40 - $tefs) * $nj / 1000,
            ScenarioUsage::DEPENSIER => 1.163 * $nadeq * 79 * (40 - $tefs) * $nj / 1000,
        };
    }

    /**
     * Coefficient d'occupation maximal
     */
    public function nmax(): float
    {
        $surface_moyenne = $this->surface_habitable() / $this->nombre_logements();

        return match ($this->audit->batiment()->type) {
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

    /**
     * Nombre d'adulte équivalent
     */
    public function nadeq(): float
    {
        return ($nmax = $this->nmax()) < 1.75
            ? $this->nombre_logements() * $nmax
            : $this->nombre_logements() * (1.75 + 0.3 * ($nmax - 1.75));
    }

    public function apply(Audit $entity): void
    {
        $this->audit = $entity;

        $entity->ecs()->calcule($entity->ecs()->data()->with(
            nmax: $this->nmax(),
            nadeq: $this->nadeq(),
            besoins: Besoins::create(
                usage: Usage::ECS,
                callback: fn(ScenarioUsage $scenario, Mois $mois) => $this->becs(
                    scenario: $scenario,
                    mois: $mois
                ),
            )
        ));
    }

    public static function dependencies(): array
    {
        return [
            ZoneThermique::class,
            ScenarioClimatique::class,
        ];
    }
}
