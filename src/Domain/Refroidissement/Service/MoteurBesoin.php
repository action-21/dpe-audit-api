<?php

namespace App\Domain\Refroidissement\Service;

use App\Domain\Common\Enum\{Mois, ScenarioUsage, Usage};
use App\Domain\Common\ValueObject\BesoinCollection;
use App\Domain\Enveloppe\Enum\Inertie;
use App\Domain\Refroidissement\Refroidissement;
use App\Domain\Simulation\Simulation;

final class MoteurBesoin
{
    public function calcule_besoins(Refroidissement $entity, Simulation $simulation): BesoinCollection
    {
        $situation = $simulation->audit()->situation();
        $gv = $simulation->enveloppe()->performance()->gv;
        $apports = $simulation->enveloppe()->apports();

        $cin = $this->cin(
            inertie: $simulation->enveloppe()->inertie()->inertie,
            surface_habitable: $entity->audit()->surface_habitable_reference(),
        );
        $t = $this->t(gv: $gv, cin: $cin);

        return BesoinCollection::create(
            usage: Usage::REFROIDISSEMENT,
            callback: fn(ScenarioUsage $scenario, Mois $mois): float => $this->bfr(
                scenario: $scenario,
                t: $t,
                as: $apports->apports_solaires_fr(scenario: $scenario, mois: $mois),
                ai: $apports->apports_internes_fr(scenario: $scenario, mois: $mois),
                gv: $gv,
                text_clim: $situation->text_fr(mois: $mois, scenario: $scenario),
                nref: $situation->nref_fr(mois: $mois, scenario: $scenario),
            )
        );
    }

    /**
     * Besoin mensuel de refroidissement en kWh
     */
    public function bfr(ScenarioUsage $scenario, float $as, float $ai, float $gv, float $t, float $nref, ?float $text_clim): float
    {
        $tint = $this->tint(scenario: $scenario);

        if (null === $text_clim)
            return 0;
        if (0.5 > ($rbth = $this->rbth(as: $as, ai: $ai, gv: $gv, tint: $tint, text_clim: $text_clim, nref: $nref)))
            return 0;

        $fut = $this->fut(rbth: $rbth, t: $t);
        return 0.5 > $rbth ? 0 : ($as + $ai) / 1000 - $fut * ($gv / 1000) * ($tint - $text_clim) * $nref;
    }

    /**
     * Ratio mensuel de bilan thermique
     */
    public function rbth(float $as, float $ai, float $gv, float $tint, float $nref, ?float $text_clim): float
    {
        if (null === $text_clim)
            return 1;
        return ($as + $ai) / ($gv * ($text_clim - $tint) * $nref);
    }

    /**
     * Facteur mensuel d'utilisation des apports
     */
    public function fut(float $rbth, float $t): float
    {
        $a = 1 + ($t / 15);
        return match (true) {
            $rbth > 0 && $rbth !== 1 => (1 - \pow($rbth, -$a)) / (1 - \pow($rbth, -$a - 1)),
            $rbth === 1 => $a / ($a + 1),
        };
    }

    /**
     * Température de consigne en froid (°C)
     */
    public function tint(ScenarioUsage $scenario): float
    {
        return match ($scenario) {
            ScenarioUsage::CONVENTIONNEL => 26,
            ScenarioUsage::DEPENSIER => 28,
        };
    }

    /**
     * Constante de temps de la zone pour le refroidissement (J/K)
     */
    public function t(float $gv, float $cin): float
    {
        return $cin / (3600 * $gv);
    }

    /**
     * Capacité thermique intérieure efficace de la zone (J/K)
     */
    public function cin(Inertie $inertie, float $surface_habitable): float
    {
        return $inertie->cin() * $surface_habitable;
    }
}
