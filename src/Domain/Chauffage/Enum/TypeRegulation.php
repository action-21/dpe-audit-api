<?php

namespace App\Domain\Chauffage\Enum;

use App\Domain\Common\Enum\Enum;

enum TypeRegulation: int implements Enum
{
    case SANS_REGULATION = 1;
    case AVEC_REGULATION = 2;

    public static function from_enum_type_regulation_id(int $id): self
    {
        return self::from($id);
    }

    public function id(): int
    {
        return $this->value;
    }

    public function lib(): string
    {
        return match ($this) {
            self::SANS_REGULATION => 'Sans régulation pièce par pièce',
            self::AVEC_REGULATION => 'Avec régulation pièce par pièce'
        };
    }
}
