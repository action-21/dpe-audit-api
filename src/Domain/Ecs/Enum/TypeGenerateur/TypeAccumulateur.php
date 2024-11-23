<?php

namespace App\Domain\Ecs\Enum\TypeGenerateur;

use App\Domain\Ecs\Enum\TypeGenerateur;

enum TypeAccumulateur: string
{
    case ACCUMULATEUR_CONDENSATION = 'ACCUMULATEUR_CONDENSATION';
    case ACCUMULATEUR_STANDARD = 'ACCUMULATEUR_STANDARD';

    public function to(): TypeGenerateur
    {
        return TypeGenerateur::from($this->value);
    }
}
