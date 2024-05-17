<?php

namespace App\Domain\Chauffage\Enum;

use App\Domain\Common\Enum\Enum;

enum EquipementIntermittence: int implements Enum
{
    case SANS = 1;
    case CENTRAL_SANS_MINIMUM_TEMPERATURE = 2;
    case CENTRAL_AVEC_MINIMUM_TEMPERATURE = 3;
    case PAR_PIECE_AVEC_MINIMUM_TEMPERATURE = 4;
    case PAR_PIECE_AVEC_MINIMUM_TEMPERATURE_ET_DETECTION_PRESENCE = 5;
    case CENTRAL_COLLECTIF = 6;
    case CENTRAL_COLLECTIF_AVEC_DETECTION_PRESENCE = 7;

    public static function from_enum_equipement_intermittence_id(int $id): self
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
            self::SANS => 'Absent',
            self::CENTRAL_SANS_MINIMUM_TEMPERATURE => 'Central sans minimum de température',
            self::CENTRAL_AVEC_MINIMUM_TEMPERATURE => 'Central avec minimum de température',
            self::PAR_PIECE_AVEC_MINIMUM_TEMPERATURE => 'Par pièce avec minimum de température',
            self::PAR_PIECE_AVEC_MINIMUM_TEMPERATURE_ET_DETECTION_PRESENCE => 'Par pièce avec minimum de température et détection de présence',
            self::CENTRAL_COLLECTIF => 'Central collectif',
            self::CENTRAL_COLLECTIF_AVEC_DETECTION_PRESENCE => 'Central collectif avec détection de présence'
        };
    }
}
