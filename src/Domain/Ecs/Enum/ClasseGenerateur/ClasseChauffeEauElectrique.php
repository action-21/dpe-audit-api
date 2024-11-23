<?php

namespace App\Domain\Ecs\Enum\ClasseGenerateur;

use App\Domain\Ecs\Enum\ClasseGenerateur;

enum ClasseChauffeEauElectrique: string
{
    case CLASSE_A = 'CLASSE_A';
    case CLASSE_B = 'CLASSE_B';
    case CLASSE_C = 'CLASSE_C';
    case INCONNU = 'INCONNU';

    public function to(): ClasseGenerateur
    {
        return ClasseGenerateur::from($this->value);
    }
}
