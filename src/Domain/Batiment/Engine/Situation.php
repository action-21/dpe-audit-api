<?php

namespace App\Domain\Batiment\Engine;

use App\Domain\Batiment\{Batiment, BatimentEngine};
use App\Domain\Batiment\Enum\ZoneClimatique;
use App\Domain\Batiment\Table\{SollicitationExterieure, SollicitationExterieureCollection, SollicitationExterieureRepository};
use App\Domain\Batiment\Table\{Tbase, TbaseRepository};
use App\Domain\Common\Enum\Mois;
use App\Domain\Common\Error\EngineTableError;
use App\Domain\Enveloppe\Enum\ClasseInertie;

/**
 * @see §18 - Annexe
 */
final class Situation
{
    private Batiment $input;
    private BatimentEngine $engine;

    private ?Tbase $table_tbase = null;
    private ?SollicitationExterieureCollection $table_ext = null;

    public function __construct(
        private SollicitationExterieureRepository $table_ext_repository,
        private TbaseRepository $table_tbase_repository,
    ) {
    }

    /**
     * Epv - Ensoleillement en kWh/m² pour le mois j (kWh/m²)
     */
    public function epv_j(Mois $mois): null|float
    {
        return $this->table_ext($mois)->epv;
    }

    /**
     * E,j - Ensoleillement reçupar une paroi verticale orientée au sud en absence d'ombrage sur le mois j (kWh/m²)
     */
    public function e_j(Mois $mois): null|float
    {
        return $this->table_ext($mois)->e;
    }

    /**
     * Efr,j - Ensoleillement reçu en période de refroidissement sur le mois j (kWh/m²)
     * 
     * @todo Vérifier la méthode (coquille ?)
     */
    public function e_fr_j(Mois $mois, bool $scenario_depensier = false): null|float
    {
        return $scenario_depensier ? $this->table_ext($mois)->efr26 : $this->table_ext($mois)->efr28;
    }

    /**
     * DH21,j - Degrés-heures de chauffage sur le mois j (°C.h)
     */
    public function dh_ch_j(Mois $mois, bool $scenario_depensier = false): null|float
    {
        return $scenario_depensier ? $this->table_ext($mois)->dh21 : $this->table_ext($mois)->dh19;
    }

    /**
     * Nref,j - Nombre d'heures de chauffage sur le mois j (h)
     */
    public function nref_ch_j(Mois $mois, bool $scenario_depensier = false): null|float
    {
        return $scenario_depensier ? $this->table_ext($mois)->nref21 : $this->table_ext($mois)->nref19;
    }

    /**
     * Nref,fr,j - Nombre d'heures de refroidissement sur le mois j (h)
     */
    public function nref_fr_j(Mois $mois, bool $scenario_depensier = false): null|float
    {
        return $scenario_depensier ? $this->table_ext($mois)->nref26 : $this->table_ext($mois)->nref28;
    }

    /**
     * Text,clim,j - Température extérieure moyenne en période de refroidissement sur le mois j (C°)
     */
    public function text_moy_clim_j(Mois $mois, bool $scenario_depensier = false): null|float
    {
        return $scenario_depensier ? $this->table_ext($mois)->textmoy_clim26 : $this->table_ext($mois)->textmoy_clim28;
    }

    /**
     * tefs,j - Température moyenne d'eau froide sanitaire sur le mois j (°C)
     */
    public function tefs_j(Mois $mois): null|float
    {
        return $this->table_ext($mois)->tefs;
    }

    /**
     * tbase - Température de base de chauffage (°C)
     */
    public function tbase(): float
    {
        return $this->table_tbase()->tbase;
    }

    /**
     * Valeur de la table bâtiment . ext
     */
    public function table_ext(Mois $mois): SollicitationExterieure
    {
        if (null === $value = $this->table_ext->get($mois)) {
            throw new EngineTableError('batiment . ext');
        }
        return $value;
    }

    /**
     * Valeurs de la table bâtiment . ext
     */
    public function table_ext_collection(): SollicitationExterieureCollection
    {
        return $this->table_ext;
    }

    /**
     * Valeur de la table bâtiment . tbase
     */
    public function table_tbase(): Tbase
    {
        if (null === $this->table_tbase) {
            throw new EngineTableError('batiment . tbase');
        }
        return $this->table_tbase;
    }

    public function fetch(): void
    {
        $this->table_tbase = $this->table_tbase_repository->find_by(
            zone_climatique: $this->zone_climatique(),
            altitude: $this->altitude(),
        );
        $this->table_ext = $this->table_ext_repository->search_by(
            zone_climatique: $this->zone_climatique(),
            altitude: $this->altitude(),
            parois_anciennes_lourdes: $this->parois_anciennes_lourdes()
        );
    }

    public function parois_anciennes_lourdes(): bool
    {
        return $this->parois_anciennes() && $this->classe_inertie()->lourde();
    }

    public function parois_anciennes(): bool
    {
        return $this->input->enveloppe()?->mur_collection()->parois_anciennes();
    }

    public function classe_inertie(): ClasseInertie
    {
        return $this->engine->context()->enveloppe_engine()->inertie()->classe_inertie();
    }

    public function zone_climatique(): ZoneClimatique
    {
        return $this->input->adresse()->zone_climatique;
    }

    public function altitude(): int
    {
        return $this->input->altitude()->valeur();
    }

    public function input(): Batiment
    {
        return $this->input;
    }

    public function engine(): BatimentEngine
    {
        return $this->engine;
    }

    public function __invoke(Batiment $input, BatimentEngine $engine): self
    {
        $service = clone $this;
        $service->input = $input;
        $service->engine = $engine;
        $service->fetch();
        return $service;
    }
}
