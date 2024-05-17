<?php

namespace App\Domain\Batiment\Engine;

use App\Domain\Batiment\{Batiment, BatimentEngine};
use App\Domain\Batiment\Enum\ZoneClimatique;
use App\Domain\Batiment\Table\{Nhecl, NheclCollection, NheclRepository};
use App\Domain\Common\Enum\Mois;
use App\Domain\Common\Error\EngineTableError;

/**
 * @see §16.1 - Consommation d’éclairage
 * @see §16.2 - Production d’électricité
 */
final class Eclairage
{
    private Batiment $input;
    private BatimentEngine $engine;

    private ?NheclCollection $table_nh_collection = null;

    /**
     * Nombre d'heures d'inoccupation conventionnel sur une semaine (h)
     */
    final public const PERIODE_INOCCUPATION_HEBDOMADAIRE = 36;

    /**
     * Puissance d'éclairage conventionnelle (W/m2)
     */
    final public const PUISSANCE_ECLAIRAGE = 1.4;

    /**
     * Coefficient correspondant au taux d'utilisation de l'éclairage en l'absence d'éclairage naturel
     */
    final public const COEFFICIENT_ECLAIRAGE_C = 0.9;

    public function __construct(
        private NheclRepository $table_nh_repository,
    ) {
    }

    /**
     * Cecl - Consommation annuelle d'éclairage (kWh)
     */
    public function cecl(): float
    {
        return \array_reduce(Mois::cases(), fn (float $carry, Mois $mois): float => $carry += $this->cecl_j($mois), 0);
    }

    /**
     * Cecl,j - Consommation d'éclairage pour le mois j (kWh)
     */
    public function cecl_j(Mois $mois): float
    {
        $cecl_j = self::COEFFICIENT_ECLAIRAGE_C * self::PUISSANCE_ECLAIRAGE * $this->becl_j($mois) * $this->surface_reference();
        return $cecl_j / 1000;
    }

    /**
     * cecl - Besoin annuel d'éclairage (h)
     */
    public function becl(): float
    {
        return \array_reduce(Mois::cases(), fn (float $carry, Mois $mois): float => $carry += (float) $this->becl_j($mois), 0);
    }

    /**
     * becl,j - Besoin d'éclairage pour le mois j (h)
     */
    public function becl_j(Mois $mois): float
    {
        return $this->nhecl_j($mois);
    }

    /**
     * Nhecl - Nombre d'heures de fonctionnement de l'éclairage sur l'année en h
     */
    public function nhecl(): float
    {
        return \array_reduce(Mois::cases(), fn (float $carry, Mois $mois): float => $carry += $this->nhecl_j($mois), 0);
    }

    /**
     * Nhecl,j - Nombre d'heures de fonctionnement de l'éclairage sur le mois j en h
     */
    public function nhecl_j(Mois $mois): float
    {
        return $this->table_nh($mois)->nhecl * $mois->jours_occupation();
    }

    /**
     * Surface habitable en m²
     */
    public function surface_reference(): float
    {
        return $this->engine->context()->surface_reference();
    }

    /**
     * Valeur de la table bâtiment . nhecl pour le mois j
     */
    public function table_nh(Mois $mois): Nhecl
    {
        if (null === $value = $this->table_nh_collection()->get($mois)) {
            throw new EngineTableError('nhecl');
        }
        return $value;
    }

    /**
     * Valeurs de la table bâtiment . nhecl
     */
    public function table_nh_collection(): NheclCollection
    {
        return $this->table_nh_collection;
    }

    public function fetch(): void
    {
        $this->table_nh_collection = $this->table_nh_repository->search_by(
            zone_climatique: $this->zone_climatique(),
        );
    }

    // * Données d'entrée

    public function zone_climatique(): ZoneClimatique
    {
        return $this->input->adresse()->zone_climatique;
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
