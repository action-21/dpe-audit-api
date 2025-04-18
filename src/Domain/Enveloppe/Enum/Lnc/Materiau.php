<?php

namespace App\Domain\Enveloppe\Enum\Lnc;

use App\Domain\Common\Enum\Enum;

enum Materiau: string implements Enum
{
    case BOIS = 'bois';
    case BOIS_METAL = 'bois_metal';
    case PVC = 'pvc';
    case METAL = 'metal';

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
