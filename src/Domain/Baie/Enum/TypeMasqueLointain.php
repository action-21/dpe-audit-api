<?php

namespace App\Domain\Baie\Enum;

use App\Domain\Common\Enum\Enum;

enum TypeMasqueLointain: string implements Enum
{
    case MASQUE_LOINTAIN_HOMOGENE = 'HOMOGENE';
    case MASQUE_LOINTAIN_NON_HOMOGENE = 'NON_HOMOGENE';

    public function id(): string
    {
        return $this->value;
    }

    public function lib(): string
    {
        return match ($this) {
            self::MASQUE_LOINTAIN_HOMOGENE => 'Masque lointain homogène',
            self::MASQUE_LOINTAIN_NON_HOMOGENE => 'Masque lointain non homogène',
        };
    }
}
