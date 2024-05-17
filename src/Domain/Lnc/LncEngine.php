<?php

namespace App\Domain\Lnc;

use App\Domain\Batiment\Enum\ZoneClimatique;
use App\Domain\Common\Enum\{Mois, Orientation};
use App\Domain\Common\Error\EngineTableError;
use App\Domain\Lnc\Engine\BaieEngineCollection;
use App\Domain\Lnc\Entity\BaieCollection;
use App\Domain\Lnc\Enum\TypeLnc;
use App\Domain\Lnc\Table\{B, BRepository, BVerCollection, BVerRepository, Uvue, UvueRepository};
use App\Domain\Simulation\SimulationEngine;

/**
 * @see §3.1 - Détermination du coefficient de réduction des déperditions b
 * @see §6.3 - Traitement des espaces tampons solarisés
 */
final class LncEngine
{
    private SimulationEngine $context;
    private Lnc $input;

    private ?Uvue $table_uvue = null;
    private ?B $table_b = null;
    private ?BVerCollection $table_bver_collection = null;

    public function __construct(
        private UvueRepository $table_uvue_repository,
        private BRepository $table_b_repository,
        private BVerRepository $table_bver_repository,
        private BaieEngineCollection $baie_engine_collection
    ) {
    }

    /**
     * Surface sud équivalente des apports dans la véranda pour le mois j
     */
    public function sst_j(Mois $mois): float
    {
        return $this->ets() ? $this->baie_engine_collection->sst_j($mois) : 0;
    }

    /**
     * t,k - Coefficient de transparence de la véranda
     */
    public function t(): float
    {
        return $this->ets() ? $this->baie_engine_collection->t() : 1;
    }

    /**
     * Coefficient de réduction des déperditions thermiques des parois donnant sur un local non chauffé
     */
    public function b(): float
    {
        if ($this->ets()) {
            return $this->bver();
        }
        if (null === $this->table_b()) {
            throw new EngineTableError('local non chauffé . b');
        }
        return $this->table_b()->valeur();
    }

    /**
     * Coefficient de réduction des déperditions thermiques des parois donnant sur un espace tampon solarisé
     */
    public function bver(): null|float
    {
        if (false === $this->ets()) {
            return null;
        }
        if (null === $bver = $this->table_bver_collection()->bver(orientation_collection: $this->orientations())) {
            throw new EngineTableError('local non chauffé . bver');
        }
        return $bver;
    }

    /**
     * Coefficient surfacique équivalent en W/(m2.K)
     */
    public function uvue(): float
    {
        if (null === $this->table_uvue) {
            throw new EngineTableError('local non chauffé . uvue');
        }
        return $this->table_uvue->valeur();
    }

    /**
     * Valeur de la table local non chauffé . uvue
     */
    public function table_uvue(): ?Uvue
    {
        return $this->table_uvue;
    }

    /**
     * Valeur de la table local non chauffé . b
     */
    public function table_b(): ?B
    {
        return $this->table_b;
    }

    /**
     * Valeur de la table local non chauffé . bver
     */
    public function table_bver_collection(): BVerCollection
    {
        return $this->table_bver_collection;
    }

    public function baie_engine_collection(): BaieEngineCollection
    {
        return $this->baie_engine_collection;
    }

    public function fetch(): void
    {
        $this->table_uvue = $this->table_uvue_repository->find_by(type_lnc: $this->type_lnc());

        $this->table_b = $this->table_uvue() ? $this->table_b_repository->find_by(
            uvue: $this->table_uvue()->valeur(),
            isolation_aiu: $this->isolation_aiu(),
            isolation_aue: $this->isolation_aue(),
            surface_aiu: $this->surface_aiu(),
            surface_aue: $this->surface_aue(),
        ) : null;

        $this->table_bver_collection = $this->ets() ? $this->table_bver_repository->search_by(
            zone_climatique: $this->zone_climatique(),
            isolation_aiu: $this->isolation_aiu(),
        ) : new BVerCollection;

        $this->baie_engine_collection = ($this->baie_engine_collection)($this->baie_collection(), $this);
    }

    // * Données d'entrée

    public function zone_climatique(): ZoneClimatique
    {
        return $this->context->input()->batiment()->adresse()->zone_climatique;
    }

    public function type_lnc(): TypeLnc
    {
        return $this->input->type_lnc();
    }

    public function ets(): bool
    {
        return $this->input->ets();
    }

    public function isolation_aiu(): bool
    {
        return $this->input->isolation_aiu();
    }

    public function isolation_aue(): bool
    {
        return $this->input->isolation_aue();
    }

    public function surface_aiu(): float
    {
        return $this->input->surface_aiu();
    }

    public function surface_aue(): float
    {
        return $this->input->surface_aue();
    }

    /** @return Orientation[] */
    public function orientations(): array
    {
        return $this->input->baie_collection()->orientations();
    }

    public function baie_collection(): BaieCollection
    {
        return $this->input->baie_collection();
    }

    public function input(): Lnc
    {
        return $this->input;
    }

    public function context(): SimulationEngine
    {
        return $this->context;
    }

    public function __invoke(Lnc $input, SimulationEngine $context): self
    {
        $engine = clone $this;
        $engine->input = $input;
        $engine->context = $context;
        $engine->fetch();
        return $engine;
    }
}
