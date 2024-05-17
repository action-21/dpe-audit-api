<?php

namespace App\Domain\Common\Enum;

enum Energie: int implements Enum
{
    case ELECTRICITE = 1;
    case GAZ_NATUREL = 2;
    case FIOUL_DOMESTIQUE = 3;
    case BOIS_BUCHES = 4;
    case BOIS_GRANULES = 5;
    case BOIS_PLAQUETTES_FORESTIERES = 6;
    case BOIS_PLAQUETTES_INDUSTRIELLES = 7;
    case RESEAU_CHAUFFAGE_URBAIN = 8;
    case PROPANE = 9;
    case BUTANE = 10;
    case CHARBON = 11;
    case ELECTRICITE_RENOUVELABLE = 12;
    case GPL = 13;
    case AUTRES_FOSSILES = 14;
    case RESEAU_FROID_URBAIN = 15;

    public function id(): int
    {
        return $this->value;
    }

    public function lib(): string
    {
        return match ($this) {
            self::ELECTRICITE => 'Électricité',
            self::GAZ_NATUREL => 'Gaz naturel',
            self::FIOUL_DOMESTIQUE => 'Fioul domestique',
            self::BOIS_BUCHES => 'Bois - Bûches',
            self::BOIS_GRANULES => 'Bois - Granulés (pellets) ou briquettes',
            self::BOIS_PLAQUETTES_FORESTIERES => 'Bois - Plaquettes forestières',
            self::BOIS_PLAQUETTES_INDUSTRIELLES => 'Bois - Plaquettes d\'industrie',
            self::RESEAU_CHAUFFAGE_URBAIN => 'Réseau de Chauffage urbain',
            self::PROPANE => 'Propane',
            self::BUTANE => 'Butane',
            self::CHARBON => 'Charbon',
            self::ELECTRICITE_RENOUVELABLE => 'Électricité d\'origine renouvelable utilisée dans le bâtiment',
            self::GPL => 'GPL',
            self::AUTRES_FOSSILES => 'Autre combustible fossile',
            self::RESEAU_FROID_URBAIN => 'Réseau de Froid Urbain',
        };
    }

    /**
     * Coefficient de conversion en PCI/PCS - Valeur pour "autre combustible fossile" non mentionnée dans la méthode officielle
     * 
     * @see §13.2.1.4
     */
    public function coefficient_conversion_pcs(): float
    {
        return match ($this) {
            self::ELECTRICITE => 1,
            self::GAZ_NATUREL => 1.11,
            self::FIOUL_DOMESTIQUE => 1.07,
            self::BOIS_BUCHES => 1.08,
            self::BOIS_GRANULES => 1.08,
            self::BOIS_PLAQUETTES_FORESTIERES => 1.08,
            self::BOIS_PLAQUETTES_INDUSTRIELLES => 1.08,
            self::RESEAU_CHAUFFAGE_URBAIN => 1,
            self::PROPANE => 1.09,
            self::BUTANE => 1.09,
            self::CHARBON => 1.04,
            self::ELECTRICITE_RENOUVELABLE => 1,
            self::GPL => 1.09,
            self::AUTRES_FOSSILES => 1,
            self::RESEAU_FROID_URBAIN => 1,
        };
    }

    /**
     * Facteur de conversion en énergie primaire
     */
    public function facteur_energie_primaire(): float
    {
        return match ($this) {
            self::ELECTRICITE => 2.3,
            default => 1
        };
    }

    public function combustible(): bool
    {
        return \in_array($this, [
            self::GAZ_NATUREL,
            self::FIOUL_DOMESTIQUE,
            self::BOIS_BUCHES,
            self::BOIS_GRANULES,
            self::BOIS_PLAQUETTES_FORESTIERES,
            self::BOIS_PLAQUETTES_INDUSTRIELLES,
            self::PROPANE,
            self::BUTANE,
            self::CHARBON,
            self::GPL,
            self::AUTRES_FOSSILES,
        ]);
    }
}
