<?php

namespace App\Domain\Enveloppe\Enum;

use App\Domain\Common\Enum\Enum;

enum TypeParoi: string implements Enum
{
    case MUR = 'mur';
    case PLANCHER_BAS = 'plancher_bas';
    case PLANCHER_HAUT = 'plancher_haut';
    case BAIE = 'baie';
    case PORTE = 'porte';

    public function id(): string
    {
        return $this->value;
    }

    public function lib(): string
    {
        return match ($this) {
            self::MUR => 'Mur',
            self::PLANCHER_BAS => 'Plancher bas',
            self::PLANCHER_HAUT => 'Plancher haut',
            self::BAIE => 'Baie',
            self::PORTE => 'Porte',
        };
    }

    public static function each(\Closure $func): array
    {
        return \array_map($func, self::cases());
    }

    /**
     * @return self[]
     */
    public static function parois_opaques(): array
    {
        return [self::MUR, self::PLANCHER_BAS, self::PLANCHER_HAUT];
    }

    /**
     * @return self[]
     */
    public static function ouvertures(): array
    {
        return [self::BAIE, self::PORTE];
    }
}
