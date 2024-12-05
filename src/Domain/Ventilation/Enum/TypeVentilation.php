<?php

namespace App\Domain\Ventilation\Enum;

use App\Domain\Common\Enum\Enum;

enum TypeVentilation: string implements Enum
{
    case VENTILATION_MECANIQUE = 'VENTILATION_MECANIQUE';
    case VENTILATION_NATURELLE_OUVERTURE_FENETRES = 'VENTILATION_NATURELLE_OUVERTURE_FENETRES';
    case VENTILATION_NATURELLE_ENTREES_AIR_HAUTES_BASSES = 'VENTILATION_NATURELLE_ENTREES_AIR_HAUTES_BASSES';
    case VENTILATION_NATURELLE_CONDUIT = 'VENTILATION_NATURELLE_CONDUIT';
    case VENTILATION_NATURELLE_CONDUIT_ENTREES_AIR_HYGROREGLABLES = 'VENTILATION_NATURELLE_CONDUIT_ENTREES_AIR_HYGROREGLABLES';

    public static function from_enum_type_ventilation_id(int $id): self
    {
        return match ($id) {
            1 => self::VENTILATION_NATURELLE_OUVERTURE_FENETRES,
            2 => self::VENTILATION_NATURELLE_ENTREES_AIR_HAUTES_BASSES,
            25 => self::VENTILATION_NATURELLE_CONDUIT,
            34 => self::VENTILATION_NATURELLE_CONDUIT_ENTREES_AIR_HYGROREGLABLES,
            default => self::VENTILATION_MECANIQUE,
        };
    }

    public function id(): string
    {
        return $this->value;
    }

    public function lib(): string
    {
        return match ($this) {
            self::VENTILATION_MECANIQUE => 'Ventilation mécanique',
            self::VENTILATION_NATURELLE_OUVERTURE_FENETRES => 'Ventilation naturelle par ouverture des fenêtres',
            self::VENTILATION_NATURELLE_ENTREES_AIR_HAUTES_BASSES => 'Ventilation naturelle par entrées d\'air hautes et basses',
            self::VENTILATION_NATURELLE_CONDUIT => 'Ventilation naturelle par conduit',
            self::VENTILATION_NATURELLE_CONDUIT_ENTREES_AIR_HYGROREGLABLES => 'Ventilation naturelle par conduit avec entrées d\'air hygroréglables',
        };
    }
}
