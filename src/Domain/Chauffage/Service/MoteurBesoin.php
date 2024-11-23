<?php

namespace App\Domain\Chauffage\Service;

use App\Domain\Chauffage\Chauffage;
use App\Domain\Common\Enum\{Mois, ScenarioUsage, Usage};
use App\Domain\Common\ValueObject\BesoinCollection;
use App\Domain\Simulation\Simulation;

final class MoteurBesoin
{
    public function calcule_besoins(Chauffage $entity, Simulation $simulation): BesoinCollection
    {
        return BesoinCollection::create(
            usage: Usage::CHAUFFAGE,
            callback: fn(ScenarioUsage $scenario, Mois $mois): float => $this->bch(
                bv: $entity->besoins_bruts()->besoins(scenario: $scenario, mois: $mois),
                dh: $simulation->audit()->situation()->dh(scenario: $scenario, mois: $mois),
                pertes_recuperables: array_sum([
                    $simulation->ecs()->generateurs()->pertes()->pertes_recuperables(scenario: $scenario, mois: $mois),
                    $simulation->ecs()->installations()->pertes()->pertes_recuperables(scenario: $scenario, mois: $mois),
                    $entity->generateurs()->pertes()->pertes_recuperables(scenario: $scenario, mois: $mois),
                ]),
            )
        );
    }

    public function calcule_besoins_bruts(Chauffage $entity, Simulation $simulation): BesoinCollection
    {
        return BesoinCollection::create(
            usage: Usage::CHAUFFAGE,
            callback: fn(ScenarioUsage $scenario, Mois $mois): float => $this->bv(
                gv: $simulation->enveloppe()->performance()->gv,
                f: $simulation->enveloppe()->apports()->f(scenario: $scenario, mois: $mois),
            )
        );
    }

    /**
     * Besoin de chauffage sur le mois en kWh PCI
     * 
     * @param float $bv - Besoin de chauffage sur le mois en W/K
     * @param float $dh - Degrés heures de chauffage sur le mois en °Ch
     * @param float $pertes_recuperables - Pertess récupérables sur le mois en Wh
     */
    public function bch(float $bv, float $dh, float $pertes_recuperables,): float
    {
        $bch = $this->bch_hp(bv: $bv, dh: $dh) - $pertes_recuperables / 1000;
        return \max(0, $bch);
    }

    /**
     * Besoin de chauffage hors pertes récupérables sur le mois en kWh PCI
     * 
     * @param float $bv - Besoin de chauffage sur le mois en W/K
     * @param float $dh - Degrés heures de chauffage sur le mois en °Ch
     */
    public function bch_hp(float $bv, float $dh,): float
    {
        return ($bv * $dh) / 1000;
    }

    /**
     * Besoin de chauffage sur le mois (W/K)
     */
    public function bv(float $gv, float $f): float
    {
        return $gv * (1 - $f);
    }
}
