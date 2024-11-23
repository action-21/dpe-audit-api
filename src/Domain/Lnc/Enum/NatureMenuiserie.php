<?php

namespace App\Domain\Lnc\Enum;

use App\Domain\Common\Enum\Enum;

enum NatureMenuiserie: string implements Enum
{
    case BOIS = 'BOIS';
    case BOIS_METAL = 'BOIS_METAL';
    case PVC = 'PVC';
    case METAL = 'METAL';

    public static function from_tv_coef_transparence_ets_id(int $id): ?self
    {
        return match ($id) {
            2, 3, 4, 5, 6 => self::BOIS,
            7, 8, 9, 10, 11 => self::PVC,
            12, 13, 14, 15, 16, 17, 18, 19, 20, 21 => self::METAL,
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
}
