<?php

namespace App\Domain\Enveloppe\Service;

use App\Domain\Common\Enum\{Mois, ScenarioUsage};
use App\Domain\Enveloppe\Enum\Inertie;
use App\Domain\Enveloppe\Enveloppe;
use App\Domain\Enveloppe\ValueObject\{Apport, ApportCollection};
use App\Domain\Baie\Service\MoteurEnsoleillement as MoteurEnsoleillementBaie;
use App\Domain\Lnc\Service\MoteurEnsoleillement as MoteurEnsoleillementLnc;
use App\Domain\Simulation\Simulation;

/**
 * @uses \App\Domain\Baie\Service\MoteurEnsoleillement
 * @uses \App\Domain\Lnc\Service\MoteurEnsoleillement
 */
final class MoteurApport
{
    public function __construct(
        private MoteurEnsoleillementLnc $moteur_ensoleillement_lnc,
        private MoteurEnsoleillementBaie $moteur_ensoleillement_baie,
    ) {}

    public final const APPORT_INTERNE_EQUIPEMENT = 3.18;
    public final const APPORT_INTERNE_ECLAIRAGE = 0.34;
    public final const APPORT_INTERNE_OCCUPANT = 90;

    public function calcule_apport(Enveloppe $entity, Simulation $simulation): ?ApportCollection
    {
        if (null === $entity->inertie())
            return null;

        $gv = $entity->performance()->gv;
        $occupation = $simulation->audit()->occupation();
        $situation = $simulation->audit()->situation();

        return ApportCollection::create(function (ScenarioUsage $scenario, Mois $mois) use ($entity, $situation, $occupation, $gv): Apport {
            $apport_solaire = $this->apport_solaire(
                sse: $entity->parois()->baies()->sse($mois),
                ensoleillement: $situation->e(mois: $mois),
            );
            $apport_interne = $this->apport_interne(
                surface_habitable: $entity->audit()->surface_habitable_reference(),
                nadeq: $occupation->nadeq,
                nref: $situation->nref(mois: $mois, scenario: $scenario),
            );
            $apport_solaire_fr = $this->apport_solaire(
                sse: $entity->parois()->baies()->sse($mois),
                ensoleillement: $situation->e_fr(mois: $mois, scenario: $scenario),
            );
            $apport_interne_fr = $this->apport_interne(
                surface_habitable: $entity->audit()->surface_habitable_reference(),
                nadeq: $occupation->nadeq,
                nref: $situation->nref_fr(mois: $mois, scenario: $scenario),
            );
            $x = $this->x(
                gv: $gv,
                as: $apport_solaire,
                ai: $apport_interne,
                dh: $situation->dh(mois: $mois, scenario: $scenario),
            );

            return Apport::create(
                mois: $mois,
                scenario: $scenario,
                f: $this->f(inertie_enveloppe: $entity->inertie()->inertie, x: $x),
                apport: $this->apport($apport_interne, $apport_solaire),
                apport_interne: $apport_interne,
                apport_solaire: $apport_solaire,
                apport_fr: $this->apport($apport_interne_fr, $apport_solaire_fr),
                apport_interne_fr: $apport_interne_fr,
                apport_solaire_fr: $apport_solaire_fr,
                sse: $entity->parois()->baies()->sse($mois)
            );
        });
    }

    public function calcule_inertie(Enveloppe $entity): Inertie
    {
        $surface_paroi_verticale = $entity->parois()->murs()->surface();
        $surface_paroi_verticale += $entity->refends()->surface();
        $surface_plancher_bas = $entity->parois()->planchers_bas()->surface();
        $surface_plancher_bas += $entity->planchers_intermediaires()->surface() * 0.5;
        $surface_plancher_haut = $entity->parois()->planchers_hauts()->surface();
        $surface_plancher_haut += $entity->planchers_intermediaires()->surface() * 0.5;

        $surface_paroi_verticale_lourde = $entity->parois()->murs()->filter_by_inertie(true)->surface();
        $surface_paroi_verticale_lourde += $entity->refends()->filter_by_inertie(true)->surface();
        $surface_plancher_bas_lourd = $entity->parois()->planchers_bas()->filter_by_inertie(true)->surface();
        $surface_plancher_bas_lourd += $entity->planchers_intermediaires()->filter_by_inertie(true)->surface() * 0.5;
        $surface_plancher_haut_lourd = $entity->parois()->planchers_hauts()->filter_by_inertie(true)->surface();
        $surface_plancher_haut_lourd += $entity->planchers_intermediaires()->filter_by_inertie(true)->surface() * 0.5;

        return $this->inertie(
            plancher_bas_lourds: $surface_plancher_bas_lourd > $surface_plancher_bas / 2,
            plancher_hauts_lourds: $surface_plancher_haut_lourd > $surface_plancher_haut / 2,
            parois_verticales_lourdes: $surface_paroi_verticale_lourde > $surface_paroi_verticale / 2,
        );
    }

    public function inertie(bool $plancher_bas_lourds, bool $plancher_hauts_lourds, bool $parois_verticales_lourdes): Inertie
    {
        return Inertie::from_inertie_parois(
            inertie_planchers_bas: $plancher_bas_lourds,
            inertie_planchers_hauts: $plancher_hauts_lourds,
            inertie_parois_verticales: $parois_verticales_lourdes,
        );
    }

    public function moteur_ensoleillement_lnc(): MoteurEnsoleillementLnc
    {
        return $this->moteur_ensoleillement_lnc;
    }

    public function moteur_ensoleillement_baie(): MoteurEnsoleillementBaie
    {
        return $this->moteur_ensoleillement_baie;
    }

    public function x(float $gv, float $as, float $ai, float $dh): float
    {
        return ($gv && $dh) ? ($as + $ai) / ($gv * $dh) : 0;
    }

    public function f(Inertie $inertie_enveloppe, float $x): float
    {
        $exposant = $inertie_enveloppe->exposant();
        $f = $x < 1 ? ($x - \pow($x, $exposant)) / (1 - \pow($x, $exposant)) : 1;
        return \min(1, $f);
    }

    public function apport(float $apport_interne, float $apport_solaire): float
    {
        return $apport_interne + $apport_solaire;
    }

    public function apport_solaire(float $sse, float $ensoleillement): float
    {
        return 1000 * $sse * $ensoleillement;
    }

    public function apport_interne(float $surface_habitable, float $nadeq, float $nref): float
    {
        $ai = (self::APPORT_INTERNE_EQUIPEMENT + self::APPORT_INTERNE_ECLAIRAGE) * $surface_habitable;
        $ai += self::APPORT_INTERNE_OCCUPANT * (132 / 168) * $nadeq;
        return $ai * $nref;
    }
}
