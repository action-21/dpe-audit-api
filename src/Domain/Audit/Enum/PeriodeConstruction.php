<?php

namespace App\Domain\Audit\Enum;

use App\Domain\Common\Enum\Enum;

enum PeriodeConstruction: string implements Enum
{
    case AVANT_1948 = 'AVANT_1948';
    case ENTRE_1948_1974 = 'ENTRE_1948_1974';
    case ENTRE_1975_1977 = 'ENTRE_1975_1977';
    case ENTRE_1978_1982 = 'ENTRE_1978_1982';
    case ENTRE_1983_1988 = 'ENTRE_1983_1988';
    case ENTRE_1989_2000 = 'ENTRE_1989_2000';
    case ENTRE_2001_2005 = 'ENTRE_2001_2005';
    case ENTRE_2006_2012 = 'ENTRE_2006_2012';
    case ENTRE_2013_2021 = 'ENTRE_2013_2021';
    case APRES_2021 = 'APRES_2021';

    public function id(): int|string
    {
        return $this->value;
    }

    public function lib(): string
    {
        return match ($this) {
            self::AVANT_1948 => 'Avant 1948',
            self::ENTRE_1948_1974 => 'Entre 1948 et 1974',
            self::ENTRE_1975_1977 => 'Entre 1975 et 1977',
            self::ENTRE_1978_1982 => 'Entre 1978 et 1982',
            self::ENTRE_1983_1988 => 'Entre 1983 et 1988',
            self::ENTRE_1989_2000 => 'Entre 1989 et 2000',
            self::ENTRE_2001_2005 => 'Entre 2001 et 2005',
            self::ENTRE_2006_2012 => 'Entre 2006 et 2012',
            self::ENTRE_2013_2021 => 'Entre 2013 et 2021',
            self::APRES_2021 => 'Après 2021',
        };
    }

    public function filter(): string
    {
        return match ($this) {
            self::AVANT_1948 => 'avant 1948',
            self::ENTRE_1948_1974 => '1948-1974',
            self::ENTRE_1975_1977 => '1975-1977',
            self::ENTRE_1978_1982 => '1978-1982',
            self::ENTRE_1983_1988 => '1983-1988',
            self::ENTRE_1989_2000 => '1989-2000',
            self::ENTRE_2001_2005 => '2001-2005',
            self::ENTRE_2006_2012 => '2006-2012',
            self::ENTRE_2013_2021 => '2013-2021',
            self::APRES_2021 => 'après 2021',
        };
    }
}
