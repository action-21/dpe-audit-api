<?php

namespace App\Domain\PontThermique;

use App\Domain\Common\Enum\Enum;
use App\Domain\Common\Error\EngineTableError;
use App\Domain\PontThermique\Table\{Kpt, KptRepository};
use App\Domain\Simulation\SimulationEngine;

/**
 * @see §3.4 - Calcul des déperditions par les ponts thermiques
 */
final class PontThermiqueEngine
{
    private PontThermique $input;
    private SimulationEngine $context;

    private ?Kpt $table_k = null;

    public function __construct(private KptRepository $table_k_repository)
    {
    }

    /**
     * PT,pt - Déperditions par le pont thermique (W/K)
     */
    public function pt(): float
    {
        return $this->l() * $this->k() * $this->coefficient_partiel();
    }

    /**
     * l,pt - Longueur du pont thermique (m)
     */
    public function l(): float
    {
        return $this->longueur();
    }

    /**
     * k,pt - Valeur du pont thermique en W/(m.K)
     */
    public function k(): float
    {
        if ($this->kpt_saisi()) {
            $this->kpt_saisi();
        }
        if (null === $this->table_k()) {
            throw new EngineTableError('pont thermique . kpt');
        }
        return $this->table_k()->valeur();
    }

    /**
     * Coefficient pour les ponts thermiques partiels
     * 
     * @see §3.4.2 - Pont thermique partiel
     */
    public function coefficient_partiel(): float
    {
        return $this->pont_thermique_partiel() ? 0.5 : 1;
    }

    /**
     * Valeur de la table pont thermique . kpt
     */
    public function table_k(): ?Kpt
    {
        return $this->table_k;
    }

    public function fetch(): void
    {
        $this->table_k = $this->table_k_repository->find_by(
            type_liaison: $this->type_liaison(),
            type_isolation_mur: $this->type_isolation_mur(),
            type_isolation_plancher: $this->type_isolation_plancher(),
            type_pose_ouverture: $this->type_pose_ouverture(),
            presence_retour_isolation: $this->presence_retour_isolation_ouverture(),
            largeur_dormant: $this->largeur_dormant_ouverture(),
        );
    }

    // * Données d'entrée

    public function type_liaison(): Enum
    {
        return $this->input->type_liaison();
    }

    public function pont_thermique_partiel(): bool
    {
        return $this->input->pont_thermique_partiel();
    }

    public function longueur(): float
    {
        return $this->input->longueur()->valeur();
    }

    public function kpt_saisi(): ?float
    {
        return $this->input->valeur()?->valeur;
    }

    public function type_isolation_mur(): ?Enum
    {
        return $this->input->mur_id() ? $this->input->enveloppe()->mur_collection()->find(
            id: $this->input->mur_id()
        )->type_isolation_defaut() : null;
    }

    public function type_isolation_plancher(): ?Enum
    {
        if (null === $id = $this->input->plancher_id()) {
            return null;
        }
        return $this->input->enveloppe()->plancher_bas_collection()->find(id: $id)?->type_isolation_defaut() ??
            $this->input->enveloppe()->plancher_haut_collection()->find(id: $id)?->type_isolation_defaut();
    }

    public function type_pose_ouverture(): ?Enum
    {
        return $this->input->ouverture_id() ? $this->input->enveloppe()->paroi_collection()->search_ouverture()->find(
            id: $this->input->ouverture_id()
        )->type_pose() : null;
    }

    public function presence_retour_isolation_ouverture(): ?bool
    {
        return $this->input->ouverture_id() ? $this->input->enveloppe()->paroi_collection()->search_ouverture()->find(
            id: $this->input->ouverture_id()
        )->presence_retour_isolation() : null;
    }

    public function largeur_dormant_ouverture(): ?bool
    {
        return $this->input->ouverture_id() ? $this->input->enveloppe()->paroi_collection()->search_ouverture()->find(
            id: $this->input->ouverture_id()
        )->largeur_dormant() : null;
    }

    public function input(): PontThermique
    {
        return $this->input;
    }

    public function context(): SimulationEngine
    {
        return $this->context;
    }

    public function __invoke(PontThermique $input, SimulationEngine $context): self
    {
        $engine = clone $this;
        $engine->input = $input;
        $engine->context = $context;
        $engine->fetch();
        return $engine;
    }
}
