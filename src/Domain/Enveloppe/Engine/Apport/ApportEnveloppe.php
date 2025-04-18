<?php

namespace App\Domain\Enveloppe\Engine\Apport;

use App\Domain\Audit\Audit;
use App\Domain\Audit\Engine\ScenarioClimatique;
use App\Domain\Common\EngineRule;
use App\Domain\Common\Enum\{Mois, ScenarioUsage};
use App\Domain\Ecs\Engine\Besoin\BesoinEcs;
use App\Domain\Enveloppe\Engine\Deperdition\DeperditionEnveloppe;
use App\Domain\Enveloppe\Engine\Inertie\InertieEnveloppe;
use App\Domain\Enveloppe\Entity\{Baie, Lnc};
use App\Domain\Enveloppe\Enum\Inertie;
use App\Domain\Enveloppe\ValueObject\{Apport, Apports};

final class ApportEnveloppe extends EngineRule
{
    public final const APPORT_INTERNE_EQUIPEMENT = 3.18;
    public final const APPORT_INTERNE_ECLAIRAGE = 0.34;
    public final const APPORT_INTERNE_OCCUPANT = 90;

    private Audit $audit;

    /**
     * @see \App\Domain\Audit\Engine\ZoneThermique::surface_habitable()
     */
    public function surface_habitable(): float
    {
        return $this->audit->data()->surface_habitable;
    }

    /**
     * @see \App\Domain\Audit\Engine\ScenarioClimatique::sollicitations_exterieures()
     */
    public function e(Mois $mois): float
    {
        return $this->audit->data()->sollicitations_exterieures->e(mois: $mois);
    }

    /**
     * @see \App\Domain\Audit\Engine\ScenarioClimatique::sollicitations_exterieures()
     */
    public function dh(ScenarioUsage $scenario, Mois $mois): float
    {
        return $this->audit->data()->sollicitations_exterieures->dh(scenario: $scenario, mois: $mois);
    }

    /**
     * @see \App\Domain\Audit\Engine\ScenarioClimatique::sollicitations_exterieures()
     */
    public function e_fr(ScenarioUsage $scenario, Mois $mois): float
    {
        return $this->audit->data()->sollicitations_exterieures->e_fr(scenario: $scenario, mois: $mois);
    }

    /**
     * @see \App\Domain\Audit\Engine\ScenarioClimatique::sollicitations_exterieures()
     */
    public function nref(ScenarioUsage $scenario, Mois $mois): float
    {
        return $this->audit->data()->sollicitations_exterieures->nref(scenario: $scenario, mois: $mois);
    }

    /**
     * @see \App\Domain\Audit\Engine\ScenarioClimatique::sollicitations_exterieures()
     */
    public function nref_fr(ScenarioUsage $scenario, Mois $mois): float
    {
        return $this->audit->data()->sollicitations_exterieures->nref_fr(scenario: $scenario, mois: $mois);
    }

    /**
     * @see \App\Domain\Ecs\Engine\BesoinEcs::nadeq()
     */
    public function nadeq(): float
    {
        return $this->audit->ecs()->data()->nadeq;
    }

    /**
     * @see \App\Domain\Enveloppe\Engine\Deperdition\DeperditionEnveloppe::gv()
     */
    public function gv(): float
    {
        return $this->audit->enveloppe()->data()->deperditions->get();
    }

    /**
     * @see \App\Domain\Enveloppe\Engine\Inertie\InertieEnveloppe::inertie()
     */
    public function inertie(): Inertie
    {
        return $this->audit->enveloppe()->data()->inertie;
    }

    /**
     * Fraction des besoins de chauffage couverts par les apports gratuits
     */
    public function f(ScenarioUsage $scenario, Mois $mois): float
    {
        $gv = $this->gv();
        $dh = $this->dh(scenario: $scenario, mois: $mois);
        $as = $this->apports_solaires(scenario: $scenario, mois: $mois);
        $ai = $this->apports_internes(scenario: $scenario, mois: $mois);
        $x = ($gv && $dh) ? ($as + $ai) / ($gv * $dh) : 0;
        $e = $this->inertie()->exposant();
        $f = $x < 1 ? ($x - \pow($x, $e)) / (1 - \pow($x, $e)) : 1;
        return \min(1, $f);
    }

    /**
     * Apports solaires mensuels en période de chauffage exprimés en Wh
     */
    public function apports_solaires(ScenarioUsage $scenario, Mois $mois): float
    {
        return 1000 * $this->sse(mois: $mois) * $this->e(mois: $mois);
    }

    /**
     * Apports solaires mensuels en période de refroidissement exprimés en Wh
     */
    public function apports_solaires_fr(ScenarioUsage $scenario, Mois $mois): float
    {
        return 1000 * $this->sse(mois: $mois) * $this->e_fr(mois: $mois, scenario: $scenario);
    }

    /**
     * Apports internes mensuels en période de chauffage exprimés en Wh
     */
    public function apports_internes(ScenarioUsage $scenario, Mois $mois): float
    {
        $nref = $this->nref(scenario: $scenario, mois: $mois);
        $ai = (self::APPORT_INTERNE_EQUIPEMENT + self::APPORT_INTERNE_ECLAIRAGE) * $this->surface_habitable();
        $ai += self::APPORT_INTERNE_OCCUPANT * (132 / 168) * $this->nadeq();
        return $ai * $nref;
    }

    /**
     * Apports internes mensuels en période de refroidissement exprimés en Wh
     */
    public function apports_internes_fr(ScenarioUsage $scenario, Mois $mois): float
    {
        $nref = $this->nref_fr(scenario: $scenario, mois: $mois);
        $ai = (self::APPORT_INTERNE_EQUIPEMENT + self::APPORT_INTERNE_ECLAIRAGE) * $this->surface_habitable();
        $ai += self::APPORT_INTERNE_OCCUPANT * (132 / 168) * $this->nadeq();
        return $ai * $nref;
    }

    /**
     * Surface sud équivalente exprimée en m²
     */
    public function sse(Mois $mois): float
    {
        $sse = 0;
        // Cas des baies donnant sur un espace tampon solarisé
        $sse += $this->audit->enveloppe()->locaux_non_chauffes()
            ->filter(fn(Lnc $entity) => $entity->type()->is_ets())
            ->reduce(fn(float $sse, Lnc $entity) => $sse + $entity->data()->ensoleillements->sse($mois));

        // Autres cas
        $sse += $this->audit->enveloppe()->baies()
            ->filter(fn(Baie $entity) => $entity->local_non_chauffe() === null)
            ->reduce(fn(float $sse, Baie $entity) => $sse + $entity->data()->ensoleillements->sse($mois));

        return $sse;
    }

    public function apply(Audit $entity): void
    {
        $this->audit = $entity;

        $apports = [];

        foreach (ScenarioUsage::cases() as $scenario) {
            foreach (Mois::cases() as $mois) {
                $apports[] = Apport::create(
                    mois: $mois,
                    scenario: $scenario,
                    f: $this->f(scenario: $scenario, mois: $mois),
                    apport_interne: $this->apports_internes(scenario: $scenario, mois: $mois),
                    apport_interne_fr: $this->apports_internes_fr(scenario: $scenario, mois: $mois),
                    apport_solaire: $this->apports_solaires(scenario: $scenario, mois: $mois),
                    apport_solaire_fr: $this->apports_solaires_fr(scenario: $scenario, mois: $mois),
                    sse: $this->sse(mois: $mois)
                );
            }
        }

        $entity->enveloppe()->calcule($entity->enveloppe()->data()->with(
            apports: Apports::create(...$apports)
        ));
    }

    public static function dependencies(): array
    {
        return [
            ScenarioClimatique::class,
            EnsoleillementBaie::class,
            EnsoleillementLnc::class,
            DeperditionEnveloppe::class,
            InertieEnveloppe::class,
            BesoinEcs::class,
        ];
    }
}
