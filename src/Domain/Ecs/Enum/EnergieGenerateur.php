<?php

namespace App\Domain\Ecs\Enum;

use App\Domain\Common\Enum\{Energie, Enum};

enum EnergieGenerateur: string implements Enum
{
    case ELECTRICITE = 'electricite';
    case GAZ_NATUREL = 'gaz_naturel';
    case GPL = 'gpl';
    case FIOUL = 'fioul';
    case BOIS_BUCHE = 'bois_buche';
    case BOIS_PLAQUETTE = 'bois_plaquette';
    case BOIS_GRANULE = 'bois_granule';
    case CHARBON = 'charbon';
    case RESEAU_CHALEUR = 'reseau_chaleur';

    public static function from_enum_type_energie_id(int $id): self
    {
        return match ($id) {
            1 => self::ELECTRICITE,
            2 => self::GAZ_NATUREL,
            3 => self::FIOUL,
            4 => self::BOIS_BUCHE,
            5 => self::BOIS_GRANULE,
            6 => self::BOIS_PLAQUETTE,
            7 => self::BOIS_PLAQUETTE,
            8 => self::RESEAU_CHALEUR,
            9 => self::GPL,
            10 => self::GPL,
            11 => self::CHARBON,
            12 => self::ELECTRICITE,
            13 => self::GPL,
        };
    }

    public function id(): string
    {
        return $this->value;
    }

    public function lib(): string
    {
        return match ($this) {
            self::ELECTRICITE => 'Électricité',
            self::GAZ_NATUREL => 'Gaz naturel',
            self::GPL => 'GPL',
            self::FIOUL => 'Fioul domestique',
            self::BOIS_BUCHE => 'Bois - Bûches',
            self::BOIS_PLAQUETTE => 'Bois - Plaquettes',
            self::BOIS_GRANULE => 'Bois - Granulés',
            self::CHARBON => 'Charbon',
            self::RESEAU_CHALEUR => 'Réseau de chaleur',
        };
    }

    public function to(): Energie
    {
        return match ($this) {
            self::BOIS_BUCHE, self::BOIS_PLAQUETTE, self::BOIS_GRANULE => Energie::BOIS,
            default => Energie::from($this->value),
        };
    }

    public function is_combustible(): bool
    {
        return match ($this) {
            self::ELECTRICITE, self::RESEAU_CHALEUR => false,
            default => true,
        };
    }

    public function is_bois(): bool
    {
        return match ($this) {
            self::BOIS_BUCHE, self::BOIS_PLAQUETTE, self::BOIS_GRANULE => true,
            default => true,
        };
    }
}
