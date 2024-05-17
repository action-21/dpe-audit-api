<?php

namespace App\Domain\Baie\Engine;

use App\Domain\Baie\BaieEngine;
use App\Domain\Baie\Enum\{NatureGazLame, NatureMenuiserie, TypeBaie, TypePose, TypeVitrage};
use App\Domain\Baie\Table\{Sw, SwRepository, UgCollection, UgRepository, UwCollection, UwRepository};
use App\Domain\Baie\ValueObject\DoubleFenetre;
use App\Domain\Common\Error\EngineTableError;

/**
 * @see §3.3 - Calcul des U des parois vitrées et des portes
 * @see §6.2 - Détermination de la surface Sud équivalente
 */
final class DoubleFenetreEngine
{
    private DoubleFenetre $input;
    private BaieEngine $engine;

    private UgCollection $table_ug_collection;
    private UwCollection $table_uw_collection;
    private ?Sw $table_sw;

    public function __construct(
        private UgRepository $table_ug_repository,
        private UwRepository $table_uw_repository,
        private SwRepository $table_sw_repository,
    ) {
    }

    /**
     * Ug - Coefficient de transmission thermique du vitrage (W/(m².K))
     */
    public function ug(): float|false
    {
        if ($this->ug_saisi()) {
            return $this->ug_saisi();
        }
        if (0 === $this->table_ug_collection()->count()) {
            throw new EngineTableError('baie . ug');
        }
        return $this->table_ug_collection()->ug(epaisseur_lame: $this->epaisseur_lame_gaz() ?? 0);
    }

    /**
     * Uw - Coefficient de transmission thermique de la double fenêtre (vitrage + menuiserie) (W/(m².K))
     */
    public function uw(): float
    {
        if ($this->uw_saisi()) {
            return $this->uw_saisi();
        }
        if (0 === $this->table_uw_collection()->count()) {
            throw new EngineTableError('baie . sw');
        }
        return $this->table_uw_collection()->uw(ug: $this->ug());
    }

    /**
     * Sw - Proportion d'énergie solaire incidente qui pénètre dans le logement par la double fenêtre
     */
    public function sw(): float
    {
        if ($this->sw_saisi()) {
            return $this->sw_saisi();
        }
        if (null === $this->table_sw()) {
            throw new EngineTableError('baie . sw');
        }
        return $this->table_sw()->valeur();
    }

    /**
     * Valeurs de la table baie . ug
     */
    public function table_ug_collection(): UgCollection
    {
        return $this->table_ug_collection;
    }

    /**
     * Valeurs de la table baie . uw
     */
    public function table_uw_collection(): UwCollection
    {
        return $this->table_uw_collection;
    }

    /**
     * Valeur forfaitaire de la table baie . sw
     */
    public function table_sw(): ?Sw
    {
        return $this->table_sw;
    }

    public function fetch(): void
    {
        $this->table_ug_collection = $this->table_ug_repository->search_by(
            type_vitrage: $this->type_vitrage(),
            nature_gaz_lame: $this->nature_gaz_lame(),
            inclinaison_vitrage: $this->inclinaison_vitrage(),
        );

        $this->table_uw_collection = $this->table_uw_repository->search_by(
            type_baie: $this->type_baie(),
            nature_menuiserie: $this->nature_menuiserie(),
        );

        $this->table_sw = $this->table_sw_repository->find_by(
            type_baie: $this->type_baie(),
            nature_menuiserie: $this->nature_menuiserie(),
            type_pose: $this->type_pose(),
            type_vitrage: $this->type_vitrage(),
        );
    }

    // * Données d'entrée

    public function nature_menuiserie(): NatureMenuiserie
    {
        return $this->input()->nature_menuiserie;
    }

    public function type_baie(): TypeBaie
    {
        return $this->input()->type_baie;
    }

    public function type_vitrage(): TypeVitrage
    {
        return $this->input()->type_vitrage;
    }

    public function nature_gaz_lame(): ?NatureGazLame
    {
        return $this->input()->nature_gaz_lame;
    }

    public function type_pose(): TypePose
    {
        return $this->input()->type_pose;
    }

    public function epaisseur_lame_gaz(): ?float
    {
        return $this->input()->epaisseur_lame?->valeur();
    }

    public function inclinaison_vitrage(): float
    {
        return $this->input()->inclinaison_vitrage->valeur();
    }

    public function ug_saisi(): ?float
    {
        return $this->input()->ug?->valeur();
    }

    public function uw_saisi(): ?float
    {
        return $this->input()->uw?->valeur();
    }

    public function sw_saisi(): ?float
    {
        return $this->input()->sw?->valeur();
    }

    public function input(): DoubleFenetre
    {
        return $this->input;
    }

    public function engine(): BaieEngine
    {
        return $this->engine;
    }

    public function __invoke(DoubleFenetre $input, BaieEngine $engine): self
    {
        $service = clone $this;
        $service->input = $input;
        $service->engine = $engine;
        $service->fetch();
        return $service;
    }
}
