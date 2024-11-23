<?php

namespace App\Domain\Baie\Enum;

use App\Domain\Common\Enum\Enum;

enum NatureMenuiserie: string implements Enum
{
    case BOIS = 'BOIS';
    case BOIS_METAL = 'BOIS_METAL';
    case PVC = 'PVC';
    case METAL = 'METAL';

    public static function from_enum_type_materiaux_menuiserie_id(int $id): ?self
    {
        return match ($id) {
            1, 2 => null,
            3 => self::BOIS,
            4 => self::BOIS_METAL,
            5 => self::PVC,
            6 => self::METAL,
            7 => self::METAL,
        };
    }

    public function id(): string
    {
        return $this->value;
    }

    public function lib(): string
    {
        return match ($this) {
            self::BOIS => 'Bois',
            self::BOIS_METAL => 'Bois/métal',
            self::PVC => 'PVC',
            self::METAL => 'Métal',
        };
    }

    /**
     * @return TypeVitrage[]
     */
    public function types_vitrage_applicables(): array
    {
        return match ($this) {
            default => [
                TypeVitrage::SIMPLE_VITRAGE,
                TypeVitrage::DOUBLE_VITRAGE,
                TypeVitrage::DOUBLE_VITRAGE_FE,
                TypeVitrage::TRIPLE_VITRAGE,
                TypeVitrage::TRIPLE_VITRAGE_FE,
            ],
        };
    }
}
