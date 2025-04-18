<?php

namespace App\Domain\Enveloppe\Enum\Baie;

use App\Domain\Common\Enum\Enum;

enum TypeMasqueLointain: string implements Enum
{
    case MASQUE_LOINTAIN_HOMOGENE = 'homogene';
    case MASQUE_LOINTAIN_NON_HOMOGENE = 'non_homogene';

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
