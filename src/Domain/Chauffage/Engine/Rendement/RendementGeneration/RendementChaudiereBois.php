<?php

namespace App\Domain\Chauffage\Engine\Rendement\RendementGeneration;

use App\Domain\Chauffage\Engine\Performance\PerformanceChaudiere;
use App\Domain\Chauffage\Engine\Performance\PerformancePac;
use App\Domain\Chauffage\Entity\Systeme;
use App\Domain\Chauffage\Enum\TypeGenerateur;
use App\Domain\Common\Enum\ScenarioUsage;

final class RendementChaudiereBois extends RendementCombustion
{
    use RendementPacHybride;

    /**
     * @see \App\Domain\Chauffage\Engine\Performance\PerformancePac::scop()
     */
    public function rg(ScenarioUsage $scenario): float
    {
        if ($this->generateur()->type()->is_pac_hybride()) {
            $rg_partie_chaudiere = $this->rg_combustion($scenario) * self::taux_couverture_partie_chaudiere(
                zone_climatique: $this->audit->adresse()->zone_climatique,
            );
            $rg_partie_pac = $this->generateur()->data()->scop * self::taux_couverture_partie_pac(
                zone_climatique: $this->audit->adresse()->zone_climatique,
            );
            return $rg_partie_chaudiere + $rg_partie_pac;
        }
        return $this->rg_combustion($scenario);
    }

    public function qp(float $x): float
    {
        $qp0 = $this->qp0();
        $pn = $this->pn();
        $rpn = $this->rpn();
        $rpint = $this->rpint();

        if ($x == 50) {
            return 0.5 * $pn * ((100 - $rpint) / $rpint);
        }
        if ($x == 100) {
            return $pn * ((100 - $rpn) / $rpn);
        }
        if ($x < 50) {
            $qp50 = $this->qp(50);
            return ((($qp50 - 0.15 * $qp0) * $x) / 0.5) + 0.15 * $qp0;
        }
        if ($x < 100) {
            $qp50 = $this->qp(50);
            $qp100 = $this->qp(100);
            return ((($qp100 - $qp50) * $x) / 0.5) + 2 * $qp50 - $qp100;
        }
        return 0;
    }

    public static function dependencies(): array
    {
        return parent::dependencies() + [PerformanceChaudiere::class, PerformancePac::class];
    }

    public static function supports(Systeme $systeme): bool
    {
        $type_generateur = $systeme->generateur()->type()->is_pac_hybride()
            ? TypeGenerateur::CHAUDIERE
            :  $systeme->generateur()->type();

        $energie_generateur =  $systeme->generateur()->type()->is_pac_hybride()
            ? $systeme->generateur()->energie_partie_chaudiere()
            : $systeme->generateur()->energie();

        return $type_generateur->is_chaudiere()
            && $energie_generateur->is_bois()
            && false === $systeme->generateur()->position()->generateur_multi_batiment;
    }
}
