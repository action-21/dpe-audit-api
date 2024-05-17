<?php

namespace App\Domain\Enveloppe\Engine;

use App\Domain\Batiment\Engine\Eclairage;
use App\Domain\Common\Enum\Mois;
use App\Domain\Enveloppe\{Enveloppe, EnveloppeEngine};
use App\Domain\Enveloppe\Enum\ClasseInertie;

/**
 * @see §6 - Détermination des apports gratuits
 * @see §11.1 - Calcul du besoin d'ECS
 * @see §16.1 - Consommation d'éclairage
 * 
 * @see Deperdition
 * @see Inertie
 * @see \App\Domain\Batiment\Engine\Occupation
 * @see \App\Domain\Batiment\Engine\Situation
 */
final class Apport
{
    private Enveloppe $input;
    private EnveloppeEngine $engine;

    /**
     * Puissance convenionnelle de chaleur par nombre d'adultes équivalents (W)
     */
    final public const PUISSANCE_CHALEUR_NADEQ = 90;

    /**
     * Puissance conventionnelle de chaleur dégagée par l'ensemble des équipements en période d'occupation hors période de sommeil (W/m²)
     */
    final public const PUISSANCE_CHALEUR_EQUIPEMENT_OCCUPATION = 5.7;

    /**
     * Puissance conventionnelle de chaleur dégagée par l'ensemble des équipements en période d'inoccupation (W/m²)
     */
    final public const PUISSANCE_CHALEUR_EQUIPEMENT_INOCCUPATION = 1.1;

    /**
     * Puissance conventionnelle de chaleur dégagée par l'ensemble des équipements pendant le sommeil (W/m²)
     */
    final public const PUISSANCE_CHALEUR_EQUIPEMENT_SOMMEIL = 1.1;

    /**
     * Nombre d'heures d'occupation conventionnel sur une semaine (h)
     */
    final public const PERIODE_OCCUPATION_HEBDOMADAIRE = 76;

    /**
     * Nombre d'heures d'inoccupation conventionnel sur une semaine (h)
     */
    final public const PERIODE_INOCCUPATION_HEBDOMADAIRE = 36;

    /**
     * Nombre d'heures de sommeil conventionnel sur une semaine (h)
     */
    final public const PERIODE_SOMMEIL_HEBDOMADAIRE = 56;

    /**
     * Puissance d'éclairage conventionnelle (W/m2)
     */
    final public const PUISSANCE_ECLAIRAGE = Eclairage::PUISSANCE_ECLAIRAGE;

    /**
     * F,j - Fraction des besoins de chauffage couverts par les apports gratuits pour le mois j
     */
    public function f_j(Mois $mois, bool $scenario_depensier = false): float
    {
        $exposant = $this->classe_inertie()->exposant();
        $xj = ($gv = $this->gv() * $this->dh_ch_j($mois, $scenario_depensier)) > 0 ? $this->apport_ch_j($mois, $scenario_depensier) / $gv : 0;
        return ($xj - \pow($xj, $exposant)) / (1 - \pow($xj, $exposant));
    }

    /**
     * Apports moyens annuels sur la période de chauffe (Wh)
     */
    public function apport_ch(bool $scenario_depensier = false): float
    {
        return $this->apport_solaire_ch($scenario_depensier) + $this->apport_interne_ch($scenario_depensier);
    }

    /**
     * Apports moyens annuels sur la période de refroidissement (Wh)
     */
    public function apport_fr(bool $scenario_depensier = false): float
    {
        return $this->apport_solaire_fr($scenario_depensier) + $this->apport_interne_fr($scenario_depensier);
    }

    /**
     * Apports solaires moyens annuels sur la période de chauffe (Wh)
     */
    public function apport_solaire_ch(bool $scenario_depensier = false): float
    {
        return \array_reduce(Mois::cases(), fn (float $carry, Mois $mois): float => $carry += $this->apport_solaire_ch_j($mois, $scenario_depensier), 0);
    }

    /**
     * Apports internes moyens annuels sur la période de chauffe (Wh)
     */
    public function apport_interne_ch(bool $scenario_depensier = false): float
    {
        return \array_reduce(Mois::cases(), fn (float $carry, Mois $mois): float => $carry += $this->apport_interne_ch_j($mois, $scenario_depensier), 0);
    }

    /**
     * Apports solaires moyens annuels sur la période de refroidissement (Wh)
     */
    public function apport_solaire_fr(bool $scenario_depensier = false): float
    {
        return \array_reduce(Mois::cases(), fn (float $carry, Mois $mois): float => $carry += $this->apport_solaire_fr_j($mois, $scenario_depensier), 0);
    }

    /**
     * Apports internes moyens annuels sur la période de refroidissement (Wh)
     */
    public function apport_interne_fr(bool $scenario_depensier = false): float
    {
        return \array_reduce(Mois::cases(), fn (float $carry, Mois $mois): float => $carry += $this->apport_interne_fr_j($mois, $scenario_depensier), 0);
    }

    /**
     * Apports moyens sur la période de chauffe pour le mois j (Wh)
     */
    public function apport_ch_j(Mois $mois, bool $scenario_depensier = false): float
    {
        return $this->apport_solaire_ch_j($mois, $scenario_depensier) + $this->apport_interne_ch_j($mois, $scenario_depensier);
    }

    /**
     * Apports moyens sur la période de refroidissement pour le mois j (Wh)
     */
    public function apport_fr_j(Mois $mois, bool $scenario_depensier = false): float
    {
        return $this->apport_solaire_fr_j($mois, $scenario_depensier) + $this->apport_interne_fr_j($mois, $scenario_depensier);
    }

    /**
     * Apports solaires moyens sur la période de chauffe pour le mois j(Wh)
     */
    public function apport_solaire_ch_j(Mois $mois, bool $scenario_depensier = false): float
    {
        return 1000 * $this->sse_j($mois, $scenario_depensier) * $this->e_j($mois, $scenario_depensier);
    }

    /**
     * Apports solaires moyens sur la période de refroidissement pour le mois j(Wh)
     */
    public function apport_solaire_fr_j(Mois $mois, bool $scenario_depensier = false): float
    {
        return 1000 * $this->sse_j($mois, $scenario_depensier) * $this->e_fr_j($mois, $scenario_depensier);
    }

    /**
     * Apports internes moyens sur la période de chauffe pour le mois j (Wh)
     */
    public function apport_interne_ch_j(Mois $mois, bool $scenario_depensier = false): float
    {
        return $this->apport_interne_equipements() + $this->apport_interne_eclairage() + $this->apport_interne_occupant() * $this->nref_ch_j($mois, $scenario_depensier);
    }

    /**
     * Apports internes moyens sur la période de refroidissement pour le mois j (Wh)
     */
    public function apport_interne_fr_j(Mois $mois, bool $scenario_depensier = false): float
    {
        return ($this->apport_interne_equipements() + $this->apport_interne_eclairage() + $this->apport_interne_occupant()) * $this->nref_fr_j($mois, $scenario_depensier);
    }

    /**
     * Apports internes moyens dus aux équipements sur une semaine type (W)
     */
    public function apport_interne_equipements(): float
    {
        $puissance = self::PUISSANCE_CHALEUR_EQUIPEMENT_OCCUPATION * (self::PERIODE_OCCUPATION_HEBDOMADAIRE / 168);
        $puissance += self::PUISSANCE_CHALEUR_EQUIPEMENT_INOCCUPATION * (self::PERIODE_INOCCUPATION_HEBDOMADAIRE / 168);
        $puissance += self::PUISSANCE_CHALEUR_EQUIPEMENT_SOMMEIL * (self::PERIODE_SOMMEIL_HEBDOMADAIRE / 168);
        return $puissance * $this->surface_reference();
    }

    /**
     * Apports internes moyens dus à l'éclairage (W)
     */
    public function apport_interne_eclairage(): float
    {
        return self::PUISSANCE_ECLAIRAGE * (2123 / 8760) * $this->surface_reference();
    }

    /**
     * Apports internes moyens dus aux occupants du logement (W)
     */
    public function apport_interne_occupant(): float
    {
        return self::PUISSANCE_CHALEUR_NADEQ * $this->nadeq() * (2123 / 8760);
    }

    /**
     * Sommes des surfaces sud équivalentes en m²
     */
    public function sse(bool $scenario_depensier = false): float
    {
        return \array_reduce(Mois::cases(), fn (float $carry, Mois $mois): float => $carry += $this->sse_j($mois, $scenario_depensier), 0);
    }

    /**
     * Sommes des surfaces sud équivalentes en m² pour le mois j
     */
    public function sse_j(Mois $mois, bool $scenario_depensier = false): float
    {
        return $this->engine->context()->baie_engine_collection()->sse_j($mois, $scenario_depensier);
    }

    /**
     * @see Deperdition
     */
    public function gv(): float
    {
        return $this->engine->deperdition()->gv();
    }

    /**
     * @see Inertie
     */
    public function classe_inertie(): ClasseInertie
    {
        return $this->engine->inertie()->classe_inertie();
    }

    /**
     * @see \App\Domain\Batiment\Engine\Occupation
     */
    public function nadeq(): null|float
    {
        return $this->engine->context()->batiment_engine()->occupation()->nadeq();
    }

    /**
     * @see \App\Domain\Batiment\Engine\Occupation
     */
    public function dh_ch_j(Mois $mois, bool $scenario_depensier = false): null|float
    {
        return $this->engine->context()->batiment_engine()->situation()->dh_ch_j($mois, $scenario_depensier);
    }

    /**
     * @see \App\Domain\Batiment\Engine\Occupation
     */
    public function e_j(Mois $mois, bool $scenario_depensier = false): null|float
    {
        return $this->engine->context()->batiment_engine()->situation()->e_j($mois, $scenario_depensier);
    }

    /**
     * @see \App\Domain\Batiment\Engine\Occupation
     */
    public function e_fr_j(Mois $mois, bool $scenario_depensier = false): null|float
    {
        return $this->engine->context()->batiment_engine()->situation()->e_fr_j($mois, $scenario_depensier);
    }

    /**
     * @see \App\Domain\Batiment\Engine\Occupation
     */
    public function nref_ch_j(Mois $mois, bool $scenario_depensier = false): null|float
    {
        return $this->engine->context()->batiment_engine()->situation()->nref_ch_j($mois, $scenario_depensier);
    }

    /**
     * @see \App\Domain\Batiment\Engine\Occupation
     */
    public function nref_fr_j(Mois $mois, bool $scenario_depensier = false): null|float
    {
        return $this->engine->context()->batiment_engine()->situation()->nref_fr_j($mois, $scenario_depensier);
    }

    /**
     * Surface de référence en m²
     */
    public function surface_reference(): float
    {
        return $this->input()->batiment()->surface_habitable();
    }

    public function input(): Enveloppe
    {
        return $this->input;
    }

    public function engine(): EnveloppeEngine
    {
        return $this->engine;
    }

    public function __invoke(Enveloppe $input, EnveloppeEngine $engine): self
    {
        $service = clone $this;
        $service->input = $input;
        $service->engine = $engine;
        return $service;
    }
}
