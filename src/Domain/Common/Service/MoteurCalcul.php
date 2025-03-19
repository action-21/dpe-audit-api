<?php

namespace App\Domain\Common\Service;

use App\Domain\Common\ValueObject\ValeursForfaitaires;

abstract class MoteurCalcul
{
    private ValeursForfaitaires $valeurs_forfaitaires;

    public function valeurs_forfaitaires(): ValeursForfaitaires
    {
        return $this->valeurs_forfaitaires ??= ValeursForfaitaires::create();
    }
}
