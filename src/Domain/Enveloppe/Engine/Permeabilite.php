<?php

namespace App\Domain\Enveloppe\Engine;

use App\Domain\Batiment\Enum\TypeBatiment;
use App\Domain\Common\Error\EngineTableError;
use App\Domain\Enveloppe\{Enveloppe, EnveloppeEngine};
use App\Domain\Enveloppe\Table\{Q4paConv, Q4paConvRepository};
use App\Domain\Paroi\Enum\TypeParoi;

/**
 * @see §4 - Calcul des déperditions par renouvellement d'air
 */
final class Permeabilite
{
    private Enveloppe $input;
    private EnveloppeEngine $engine;

    private ?Q4paConv $table_q4pa_conv;

    public function __construct(private Q4paConvRepository $table_q4pa_conv_repository)
    {
    }

    /**
     * Hvent : déperdition thermique par renouvellement d'air due au système de ventilation par degré d'écart entre l'intérieur et l'extérieur en W/K
     */
    public function hvent(): float
    {
        return 0.34 * $this->qvarep_conv() * $this->surface_reference();
    }

    /**
     * Hperm : déperdition thermique par renouvellement d'air due au vent par degré d'écart entre l'intérieur et l'extérieur en W/K
     */
    public function hperm(): float
    {
        return 0.34 * $this->qvinf();
    }

    /**
     * Qvinf : débit d'air dû aux infiltrations liées au vent en m3/h
     */
    public function qvinf(): float
    {
        return ($this->volume_reference() * $this->n50() * $this->e())
            / (1 + $this->f() / $this->e() * \pow(($this->qvasouf_conv() - $this->qvarep_conv()) / ($this->hauteur_reference() * $this->n50()), 2));
    }

    /**
     * Renouvellement d'air sous 50 Pascals (h-1)
     */
    public function n50(): float
    {
        return $this->q4pa() / (\pow(4 / 50, 2 / 3) * $this->volume_reference());
    }

    /**
     * Q4pa : perméabilité sous 4 Pa de la zone en m3/h
     */
    public function q4pa(): float
    {
        return $this->q4pa_env() + 0.45 * $this->smea_conv() * $this->surface_reference();
    }

    /**
     * Q4Paenv : perméabilité de l'enveloppe en m3/h
     */
    public function q4pa_env(): float
    {
        return $this->q4pa_conv() * $this->sdep();
    }

    /**
     * Valeur conventionnelle de la perméabilité sous 4Pa en m3/(h.m2)
     */
    public function q4pa_conv(): float
    {
        if ($this->input->permeabilite()->q4pa_conv) {
            return $this->input->permeabilite()->q4pa_conv;
        }
        if (null === $this->table_q4pa_conv()) {
            throw new EngineTableError('enveloppe . q4pa_conv');
        }
        return $this->table_q4pa_conv()->q4pa_conv;
    }

    /**
     * Coefficient de protection
     */
    public function e(): float
    {
        return $this->exposition()->coefficient_e();
    }

    /**
     * Coefficient de protection
     */
    public function f(): float
    {
        return $this->exposition()->coefficient_f();
    }

    /**
     * Valeur forfaitaire de la table q4pa_conv
     */
    public function table_q4pa_conv(): ?Q4paConv
    {
        return $this->table_q4pa_conv;
    }

    public function fetch(): void
    {
        $this->table_q4pa_conv = $this->table_q4pa_conv_repository->find_by(
            type_batiment: $this->type_batiment(),
            annee_construction: $this->annee_construction(),
            presence_joints_menuiserie: $this->presence_joint_menuiserie(),
            isolation_murs_plafonds: $this->isolation_murs_plafonds(),
        );
    }

    // * Données d'entrée

    public function type_batiment(): TypeBatiment
    {
        return $this->input->batiment()->type_batiment();
    }

    public function annee_construction(): int
    {
        return $this->input->batiment()->annee_construction()->valeur();
    }

    public function presence_joint_menuiserie(): bool
    {
        return $this->input->paroi_collection()->search_ouverture()->presence_joint();
    }

    public function isolation_murs_plafonds(): bool
    {
        return $this->input->paroi_collection()->search_paroi_opaque()->search_without_type(TypeParoi::PLANCHER_BAS)->est_isole();
    }

    /**
     * Surface déperditive hors plancher bas en m²
     */
    public function sdep(): float
    {
        return $this->input->paroi_collection()->search_without_type(TypeParoi::PLANCHER_BAS)->surface_deperditive();
    }

    /**
     * Moyenne pondérée de qvasouf_conv
     */
    public function qvasouf_conv(): float
    {
        return $this->engine->installation_ventilation_engine_collection()->qvasouf_conv();
    }

    /**
     * Moyenne pondérée de qvarep_conv
     */
    public function qvarep_conv(): float
    {
        return $this->engine->installation_ventilation_engine_collection()->qvarep_conv();
    }

    /**
     * Moyenne pondérée de smea_conv
     */
    public function smea_conv(): float
    {
        return $this->engine->installation_ventilation_engine_collection()->smea_conv();
    }

    /**
     * Surface de référence en m²
     */
    public function surface_reference(): float
    {
        return $this->input->batiment()->surface_reference();
    }

    /**
     * Hauteur sous plafond de référence en m
     */
    public function hauteur_reference(): float
    {
        return $this->input()->batiment()->hauteur_reference();
    }

    /**
     * Volume de référence en m3
     */
    public function volume_reference(): float
    {
        return $this->input()->batiment()->volume_reference();
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
        $service->fetch();
        return $service;
    }
}
