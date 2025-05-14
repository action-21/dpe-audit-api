<?php

namespace App\Engine\Performance\Chauffage\Rendement\RendementGeneration;

use App\Domain\Chauffage\Entity\Systeme;
use App\Domain\Chauffage\Enum\TauxCharge;
use App\Domain\Common\Enum\{ScenarioUsage, ZoneClimatique};
use App\Engine\Performance\Chauffage\Performance\{PerformanceChaudiere, PerformancePac};

final class RendementChaudiereBois extends RendementChaudiere
{
    use RendementPacHybride;

    /**
     * @see \App\Engine\Performance\Scenario\ScenarioClimatique::zone_climatique()
     */
    public function zone_climatique(): ZoneClimatique
    {
        return $this->audit->data()->zone_climatique;
    }

    /**
     * @see \App\Engine\Performance\Chauffage\Performance\PerformancePac::scop()
     */
    public function rg(ScenarioUsage $scenario): float
    {
        if ($this->generateur()->type()->is_pac_hybride()) {
            $rg_partie_chaudiere = $this->rg_combustion($scenario) * self::taux_couverture_partie_chaudiere(
                zone_climatique: $this->zone_climatique(),
            );
            $rg_partie_pac = $this->generateur()->data()->scop * self::taux_couverture_partie_pac(
                zone_climatique: $this->zone_climatique(),
            );
            return $rg_partie_chaudiere + $rg_partie_pac;
        }
        return $this->rg_combustion($scenario);
    }

    public function qp(ScenarioUsage $scenario, TauxCharge $x): float
    {
        $QP0 = $this->qp0();
        $QP50 = $this->qp50();
        $QP100 = $this->qp100();
        $tch = $this->tch_final(scenario: $scenario, x: $x);

        return $x->value < 50
            ? ((($QP50 - 0.15 * $QP0) * $tch) / 0.5) + 0.15 * $QP0
            : ((($QP100 - $QP50) * $tch) / 0.5) + 2 * $QP50 - $QP100;
    }

    /**
     * Pertes de charge à 50% de puissance
     */
    public function qp50(): float
    {
        $pn = $this->pn();
        $rpint = $this->rpint();
        return 0.5 * $pn * ((100 - $rpint) / $rpint);
    }

    /**
     * Pertes de charge à 100% de puissance
     */
    public function qp100(): float
    {
        $pn = $this->pn();
        $rpn = $this->rpn();
        return $pn * ((100 - $rpn) / $rpn);
    }

    public static function dependencies(): array
    {
        return parent::dependencies() + [PerformanceChaudiere::class, PerformancePac::class];
    }

    public static function match(Systeme $systeme): bool
    {
        return parent::match($systeme) && $systeme->generateur()->energie()->is_bois();
    }
}
