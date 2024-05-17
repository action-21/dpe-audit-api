<?php

namespace App\Domain\PlancherIntermediaire\Enum;

use App\Domain\Common\Enum\Enum;

enum TypePlancherIntermediaire: int implements Enum
{
    case SOUS_FACE = 1;
    case FACE_SUPERIEURE = 2;

    public function id(): int
    {
        return $this->value;
    }

    public function lib(): string
    {
        return match ($this) {
            self::SOUS_FACE => "Sous-face de plancher intermédiaire sans isolant et sans faux plafond",
            self::FACE_SUPERIEURE => "Face supérieure de plancher intermédiaire avec un revêtement non isolant",
        };
    }
}
