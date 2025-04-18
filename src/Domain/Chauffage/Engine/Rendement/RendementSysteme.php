<?php

namespace App\Domain\Chauffage\Engine\Rendement;

use App\Domain\Audit\Audit;
use App\Domain\Chauffage\Entity\Systeme;
use App\Domain\Chauffage\ValueObject\{Rendement, Rendements};
use App\Domain\Common\EngineRule;
use App\Domain\Common\Enum\ScenarioUsage;

final class RendementSysteme extends EngineRule
{
    protected Systeme $systeme;

    /**
     * @see \App\Domain\Chauffage\Engine\Rendement\RendementDistribution::rd()
     */
    public function rd(): float
    {
        return $this->systeme->data()->rd;
    }

    /**
     * @see \App\Domain\Chauffage\Engine\Rendement\RendementEmission::re()
     */
    public function re(): float
    {
        return $this->systeme->data()->re;
    }

    /**
     * @see \App\Domain\Chauffage\Engine\Rendement\RendementGeneration::rg()
     */
    public function rg(ScenarioUsage $scenario): float
    {
        return $this->systeme->data()->rg->get($scenario);
    }

    /**
     * @see \App\Domain\Chauffage\Engine\Rendement\RendementRegulation::rr()
     */
    public function rr(): float
    {
        return $this->systeme->data()->rr;
    }

    /**
     * Inverse du rendement du système
     */
    public function ich(ScenarioUsage $scenario): float
    {
        return 1 / array_product([
            $this->rg($scenario),
            $this->rd(),
            $this->re(),
            $this->rr(),
        ]);
    }

    public function apply(Audit $entity): void
    {
        foreach ($entity->chauffage()->systemes() as $systeme) {
            $this->systeme = $systeme;

            $rendements = ScenarioUsage::each(fn(ScenarioUsage $scenario) => Rendement::create(
                scenario: $scenario,
                rendement: $this->ich($scenario),
            ));

            $systeme->calcule($systeme->data()->with(
                ich: Rendements::create(...$rendements),
            ));
        }
    }

    public static function dependencies(): array
    {
        return [
            RendementDistribution::class,
            RendementEmission::class,
            RendementRegulation::class,
            RendementGeneration\RendementChaudiere::class,
            RendementGeneration\RendementChaudiereBois::class,
            RendementGeneration\RendementChaudiereElectrique::class,
            RendementGeneration\RendementGenerateurAirChaud::class,
            RendementGeneration\RendementGenerateurEffetJoule::class,
            RendementGeneration\RendementPoeleBouilleur::class,
            RendementGeneration\RendementPoeleInsert::class,
            RendementGeneration\RendementRadiateurGaz::class,
            RendementGeneration\RendementReseauChaleur::class,
        ];
    }
}
