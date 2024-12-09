<?php

namespace App\Domain\Chauffage\Enum;

use App\Domain\Common\Enum\Enum;

enum TypeChauffage: string implements Enum
{
    /**
     * Un système de chauffage divisé est un système pour lequel la génération et l’émission sont confondues. C’est
     * le cas des convecteurs électriques, planchers chauffants électriques...
     */
    case CHAUFFAGE_DIVISE = 'CHAUFFAGE_DIVISE';

    /**
     * Un système de chauffage central comporte un générateur central, individuel ou collectif, et une distribution par
     * fluide chauffant : air ou eau.
     */
    case CHAUFFAGE_CENTRAL = 'CHAUFFAGE_CENTRAL';

    public static function from_enum_type_chauffage_id(int $id): self
    {
        return match ($id) {
            1 => self::CHAUFFAGE_DIVISE,
            2 => self::CHAUFFAGE_CENTRAL,
        };
    }

    public function id(): string
    {
        return $this->value;
    }

    public function lib(): string
    {
        return match ($this) {
            self::CHAUFFAGE_DIVISE => 'Chauffage divisé',
            self::CHAUFFAGE_CENTRAL => 'Chauffage central'
        };
    }
}
