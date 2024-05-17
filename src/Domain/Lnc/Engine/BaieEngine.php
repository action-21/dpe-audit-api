<?php

namespace App\Domain\Lnc\Engine;

use App\Domain\Batiment\Enum\ZoneClimatique;
use App\Domain\Common\Enum\{Mois, Orientation};
use App\Domain\Common\Error\EngineTableError;
use App\Domain\Lnc\Entity\Baie;
use App\Domain\Lnc\Enum\{NatureMenuiserie, TypeVitrage};
use App\Domain\Lnc\LncEngine;
use App\Domain\Lnc\Table\{C1, C1Collection, C1Repository, T, TRepository};

/**
 * @see §6.3 - Traitement des espaces tampons solarisés
 */
final class BaieEngine
{
    private Baie $input;
    private LncEngine $engine;

    private ?C1Collection $table_c1_collection = null;
    private ?T $table_t = null;

    public function __construct(
        private C1Repository $table_c1_repository,
        private TRepository $table_t_repository,
    ) {
    }

    /**
     * Surface sud équivalente des apports dans la véranda par la baie k pour le mois j
     */
    public function sst_j(Mois $mois): float
    {
        return $this->surface() * (0.8 * $this->t() + 0.024) * $this->fe() * $this->c1_j($mois);
    }

    /**
     * Fe,k - Facteur d'ensoleillement qui traduit la réduction d'énergie solaire reçue par la baie k du fait des masques lointains
     */
    public function fe(): float
    {
        return 1;
    }

    /**
     * C1,k,j - Coefficient d'orientation et d'inclinaison pour le mois j
     */
    public function c1_j(Mois $mois): float
    {
        return $this->table_c1($mois)->c1;
    }

    /**
     * t,k - Coefficient de transparence
     */
    public function t(): float
    {
        return $this->table_t()->valeur();
    }

    /**
     * A,k - Surface de la baie (m²)
     */
    public function surface(): float
    {
        return $this->surface_reference();
    }

    /**
     * Valeur de la table local non chauffé . c1 pour le mois j
     */
    public function table_c1(Mois $mois): C1
    {
        if (null === $value = $this->table_c1_collection()->find($mois)) {
            throw new EngineTableError('local non chauffé . baie . c1');
        }
        return $value;
    }

    /**
     * Valeurs de la table local non chauffé . c1
     */
    public function table_c1_collection(): C1Collection
    {
        return $this->table_c1_collection;
    }

    /**
     * Valeur de la table local non chauffé . t
     */
    public function table_t(): T
    {
        if (null === $this->table_t) {
            throw new EngineTableError('local non chauffé . baie . t');
        }
        return $this->table_t;
    }

    public function fetch(): void
    {
        $this->table_t = $this->table_t_repository->find_by(
            nature_menuiserie: $this->nature_menuiserie(),
            type_vitrage: $this->type_vitrage(),
        );

        $this->table_c1_collection = $this->table_c1_repository->search_by(
            zone_climatique: $this->zone_climatique(),
            orientation: $this->orientation(),
            inclinaison: $this->inclinaison_vitrage(),
        );
    }

    // * Données d'entrée

    public function zone_climatique(): ZoneClimatique
    {
        return $this->input->local_non_chauffe()->enveloppe()->batiment()->adresse()->zone_climatique;
    }

    public function nature_menuiserie(): NatureMenuiserie
    {
        return $this->input->nature_menuiserie();
    }

    public function type_vitrage(): ?TypeVitrage
    {
        return $this->input->type_vitrage();
    }

    public function orientation(): ?Orientation
    {
        return $this->input->orientation()?->enum();
    }

    public function inclinaison_vitrage(): float
    {
        return $this->input->inclinaison_vitrage()->valeur();
    }

    public function surface_reference(): float
    {
        return $this->input->surface()->valeur();
    }

    public function input(): Baie
    {
        return $this->input;
    }

    public function engine(): LncEngine
    {
        return $this->engine;
    }

    public function __invoke(Baie $input, LncEngine $engine): self
    {
        $service = clone $this;
        $service->input = $input;
        $service->engine = $engine;
        $service->fetch();
        return $service;
    }
}
