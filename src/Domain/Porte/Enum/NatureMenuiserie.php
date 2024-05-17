<?php

namespace App\Domain\Porte\Enum;

use App\Domain\Common\Enum\Enum;

enum NatureMenuiserie: int implements Enum
{
    case PORTE_SIMPLE_BOIS = 1;
    case PORTE_SIMPLE_PVC = 2;
    case PORTE_SIMPLE_METAL = 3;
    case AUTRES = 4;

    public static function from_enum_type_porte_id(int $id): self
    {
        return match ($id) {
            1, 2, 3, 4 => self::PORTE_SIMPLE_BOIS,
            5, 6, 7, 8 => self::PORTE_SIMPLE_PVC,
            9, 10, 11, 12 => self::PORTE_SIMPLE_METAL,
            13, 14, 15, 16 => self::AUTRES,
        };
    }

    public static function path(): string
    {
        return 'porte . nature de la menuiserie';
    }

    /** @inheritdoc */
    public function id(): int
    {
        return $this->value;
    }

    /** @inheritdoc */
    public function lib(): string
    {
        return match ($this) {
            self::PORTE_SIMPLE_BOIS => 'Porte simple en bois',
            self::PORTE_SIMPLE_PVC => 'Porte simple en PVC',
            self::PORTE_SIMPLE_METAL => 'Porte simple en métal',
            self::AUTRES => 'Toute menuiserie',
        };
    }
}
