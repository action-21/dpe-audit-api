<?php

namespace App\Domain\Audit\Enum;

use App\Domain\Common\Enum\Enum;

enum MethodeCalcul: int implements Enum
{
    case _3CLDPE2021 = 1;
    case RT2012 = 2;
    case RE2020 = 3;

    public static function from_enum_methode_application_log_id(int $id): self
    {
        return match ($id) {
            18, 19, 20, 21, 24, 25 => self::RE2020,
            24, 15, 16, 17, 22, 23 => self::RT2012,
            default => self::_3CLDPE2021,
        };
    }

    public function id(): int
    {
        return $this->value;
    }

    public function lib(): string
    {
        return match ($this) {
            self::_3CLDPE2021 => '3CL-DPE 2021',
            self::RT2012 => 'RT2012',
            self::RE2020 => 'RE2020',
        };
    }
}
