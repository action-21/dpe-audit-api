<?php

namespace App\Domain\Audit\Enum;

use App\Domain\Common\Enum\Enum;

enum Perimetre: string implements Enum
{
    case BATIMENT = 'BATIMENT';
    case LOGEMENT = 'LOGEMENT';

    public static function from_enum_methode_application_dpe_log_id(int $id): self
    {
        return match ($id) {
            1, 6, 7, 8, 9, 14, 17, 18, 21, 26, 27, 28, 29, 30 => self::BATIMENT,
            default => self::LOGEMENT,
        };
    }

    public function id(): string
    {
        return $this->value;
    }

    public function lib(): string
    {
        return match ($this) {
            self::BATIMENT => 'Bâtiment',
            self::LOGEMENT => 'Logement',
        };
    }
}
