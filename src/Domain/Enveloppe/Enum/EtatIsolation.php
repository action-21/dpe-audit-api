<?php

namespace App\Domain\Enveloppe\Enum;

use App\Domain\Common\Enum\Enum;

enum EtatIsolation: string implements Enum
{
    case NON_ISOLE = 'non_isole';
    case ISOLE = 'isole';

    public static function from_enum_type_isolation_id(int $type_isolation_id): ?self
    {
        return match ($type_isolation_id) {
            2 => self::NON_ISOLE,
            3, 4, 5, 6, 7, 8 => self::ISOLE,
            default => null,
        };
    }

    public static function from_enum_type_porte_id(int $type_porte_id): ?self
    {
        return match ($type_porte_id) {
            1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12 => self::NON_ISOLE,
            13, 15 => self::ISOLE,
            default => null,
        };
    }

    public static function from_boolean(bool $isolation): self
    {
        return $isolation ? self::ISOLE : self::NON_ISOLE;
    }

    public function id(): string
    {
        return $this->value;
    }

    public function lib(): string
    {
        return match ($this) {
            self::NON_ISOLE => 'Non isolé',
            self::ISOLE => 'Isolé',
        };
    }

    public function is_isole(): bool
    {
        return $this === self::ISOLE;
    }
}
