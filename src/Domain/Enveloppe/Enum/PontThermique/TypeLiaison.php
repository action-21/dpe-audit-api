<?php

namespace App\Domain\Enveloppe\Enum\PontThermique;

use App\Domain\Common\Enum\Enum;

enum TypeLiaison: string implements Enum
{
    case PLANCHER_BAS_MUR = 'plancher_bas_mur';
    case PLANCHER_INTERMEDIAIRE_MUR = 'plancher_intermediaire_mur';
    case PLANCHER_HAUT_MUR = 'plancher_haut_mur';
    case REFEND_MUR = 'refend_mur';
    case MENUISERIE_MUR = 'menuiserie_mur';

    public static function from_enum_type_liaison_id(int $id): self
    {
        return match ($id) {
            1 => self::PLANCHER_BAS_MUR,
            2 => self::PLANCHER_INTERMEDIAIRE_MUR,
            3 => self::PLANCHER_HAUT_MUR,
            4 => self::REFEND_MUR,
            5 => self::MENUISERIE_MUR
        };
    }

    public function id(): string
    {
        return $this->value;
    }

    public function lib(): string
    {
        return match ($this) {
            self::PLANCHER_BAS_MUR => 'Plancher bas / Mur',
            self::PLANCHER_INTERMEDIAIRE_MUR => 'Plancher Intermédiaire lourd / Mur',
            self::PLANCHER_HAUT_MUR => 'Plancher haut lourd / Mur',
            self::REFEND_MUR => 'Refend / Mur',
            self::MENUISERIE_MUR => 'Menuiserie / Mur'
        };
    }
}
