<?php

namespace App\Domain\PontThermique\Enum;

use App\Domain\Common\Enum\Enum;

enum TypeLiaison: string implements Enum
{
    case PLANCHER_BAS_MUR = 'PLANCHER_BAS_MUR';
    case PLANCHER_INTERMEDIAIRE_MUR = 'PLANCHER_INTERMEDIAIRE_MUR';
    case PLANCHER_HAUT_MUR = 'PLANCHER_HAUT_MUR';
    case REFEND_MUR = 'REFEND_MUR';
    case MENUISERIE_MUR = 'MENUISERIE_MUR';

    public static function from_enum_type_liaison_id(int $type_liaison_id): self
    {
        return match ($type_liaison_id) {
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
