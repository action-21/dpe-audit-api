<?php

namespace App\Domain\Refroidissement\Engine;

use App\Domain\Audit\Audit;
use App\Domain\Audit\Engine\{ScenarioClimatique, ZoneThermique};
use App\Domain\Common\EngineRule;
use App\Domain\Common\Enum\{Mois, ScenarioUsage, Usage};
use App\Domain\Common\ValueObject\Besoins;
use App\Domain\Enveloppe\Engine\Apport\ApportEnveloppe;
use App\Domain\Enveloppe\Engine\Deperdition\DeperditionEnveloppe;
use App\Domain\Enveloppe\Engine\Inertie\InertieEnveloppe;
use App\Domain\Enveloppe\Enum\Inertie;

final class BesoinRefroidissement extends EngineRule
{
    private Audit $audit;

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
     * @see \App\Domain\Audit\Engine\ZoneThermique::surface_habitable()
     */
    public function surface_habitable(): float
    {
        return $this->audit->data()->surface_habitable;
    }

    /**
     * @see \App\Domain\Enveloppe\Engine\Apport\ApportEnveloppe::apports_internes()
     * @see \App\Domain\Enveloppe\Engine\Apport\ApportEnveloppe::apports_solaires()
     */
    public function apports(ScenarioUsage $scenario, Mois $mois): float
    {
        return $this->audit->enveloppe()->data()->apports->apports_fr(
            scenario: $scenario,
            mois: $mois,
        );
    }

    /**
     * @see \App\Domain\Audit\Engine\ScenarioClimatique::sollicitations_exterieures()
     */
    public function text_fr(ScenarioUsage $scenario, Mois $mois): ?float
    {
        return $this->audit->data()->sollicitations_exterieures->text_fr(scenario: $scenario, mois: $mois);
    }

    /**
     * @see \App\Domain\Audit\Engine\ScenarioClimatique::sollicitations_exterieures()
     */
    public function nref_fr(ScenarioUsage $scenario, Mois $mois): ?float
    {
        return $this->audit->data()->sollicitations_exterieures->nref_fr(scenario: $scenario, mois: $mois);
    }

    /**
     * Besoin mensuel de refroidissement exprimé en kWh
     */
    public function bfr(ScenarioUsage $scenario, Mois $mois,): float
    {
        $text_clim = $this->text_fr(scenario: $scenario, mois: $mois);
        $nref = $this->nref_fr(scenario: $scenario, mois: $mois);

        if (null === $text_clim) {
            return 0;
        }
        if (0.5 > ($rbth = $this->rbth(scenario: $scenario, mois: $mois))) {
            return 0;
        }
        $fut = $this->fut(scenario: $scenario, mois: $mois);
        $tint = $this->tint(scenario: $scenario);

        if (0.5 > $rbth) {
            return 0;
        }
        $bfr = $this->apports(scenario: $scenario, mois: $mois) / 1000;
        $bfr -= $fut * ($this->gv() / 1000) * ($tint - $text_clim) * $nref;
        return max($bfr, 0);
    }

    /**
     * Ratio mensuel de bilan thermique
     */
    public function rbth(ScenarioUsage $scenario, Mois $mois,): float
    {
        $tint = $this->tint(scenario: $scenario);
        $text_clim = $this->text_fr(scenario: $scenario, mois: $mois);
        $nref = $this->nref_fr(scenario: $scenario, mois: $mois);

        $rbth = $this->gv() * ($text_clim - $tint) * $nref;
        return $rbth ? $this->apports(scenario: $scenario, mois: $mois) / $rbth : 0;
    }

    /**
     * Facteur mensuel d'utilisation des apports
     */
    public function fut(ScenarioUsage $scenario, Mois $mois,): float
    {
        $t = $this->t();
        $rbth = $this->rbth(scenario: $scenario, mois: $mois);

        $a = 1 + ($t / 15);
        return match (true) {
            $rbth > 0 && $rbth !== 1 => (1 - \pow($rbth, -$a)) / (1 - \pow($rbth, -$a - 1)),
            $rbth === 1 => $a / ($a + 1),
        };
    }

    /**
     * Température de consigne en froid exprimée en °C
     */
    public function tint(ScenarioUsage $scenario): float
    {
        return match ($scenario) {
            ScenarioUsage::CONVENTIONNEL => 26,
            ScenarioUsage::DEPENSIER => 28,
        };
    }

    /**
     * Constante de temps de la zone pour le refroidissement exprimée en J/K
     */
    public function t(): float
    {
        return $this->cin() / (3600 * $this->gv());
    }

    /**
     * Capacité thermique intérieure efficace de la zone exprimée en J/K
     */
    public function cin(): float
    {
        return $this->inertie()->cin() * $this->surface_habitable();
    }

    public function apply(Audit $entity): void
    {
        $this->audit = $entity;

        $entity->refroidissement()->calcule($entity->refroidissement()->data()->with(
            besoins: Besoins::create(
                usage: Usage::REFROIDISSEMENT,
                callback: fn(ScenarioUsage $scenario, Mois $mois) => $this->bfr(
                    scenario: $scenario,
                    mois: $mois,
                ),
            ),
        ));
    }

    public static function dependencies(): array
    {
        return [
            ZoneThermique::class,
            ScenarioClimatique::class,
            DeperditionEnveloppe::class,
            ApportEnveloppe::class,
            InertieEnveloppe::class,
        ];
    }
}
