<?php

namespace App\Engine\Performance\Chauffage\Rendement\RendementGeneration;

use App\Domain\Chauffage\Entity\Systeme;
use App\Domain\Chauffage\Enum\TypeGenerateur;
use App\Domain\Common\Enum\{ScenarioUsage, ZoneClimatique};
use App\Engine\Performance\Chauffage\Performance\{PerformanceChaudiere, PerformancePac};

abstract class RendementChaudiere extends RendementCombustion
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
     * @see \App\Engine\Performance\Chauffage\Performance\PerformanceChaudiere::tfonc30()
     */
    public function tfonc30(): float
    {
        return $this->generateur()->data()->tfonc30;
    }

    /**
     * @see \App\Engine\Performance\Chauffage\Performance\PerformanceChaudiere::tfonc100()
     */
    public function tfonc100(): float
    {
        return $this->generateur()->data()->tfonc100;
    }

    /**
     * Présence d'une régulation
     */
    public function regulation(): bool
    {
        return $this->installation()->regulation_centrale()->presence_regulation
            || $this->installation()->regulation_terminale()->presence_regulation;
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

    /**
     * Pertes de charge à 100% de puissance
     */
    public function qp100(): float
    {
        $pn = $this->pn();
        $rpn = $this->rpn();
        $tfonc = $this->tfonc100();

        $qp = 100 - ($rpn + 0.1 * (70 - $tfonc));
        $qp /= $rpn + 0.1 * (70 - $tfonc);
        $qp *= $pn;

        return $qp;
    }

    public static function dependencies(): array
    {
        return parent::dependencies() + [PerformanceChaudiere::class, PerformancePac::class];
    }

    public static function match(Systeme $systeme): bool
    {
        $type_generateur = $systeme->generateur()->type()->is_pac_hybride()
            ? TypeGenerateur::CHAUDIERE
            :  $systeme->generateur()->type();

        $energie_generateur =  $systeme->generateur()->type()->is_pac_hybride()
            ? $systeme->generateur()->energie_partie_chaudiere()
            : $systeme->generateur()->energie();

        return $type_generateur->is_chaudiere()
            && $energie_generateur->is_combustible()
            && false === $systeme->generateur()->position()->generateur_multi_batiment;
    }
}
