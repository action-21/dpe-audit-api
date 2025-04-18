<?php

namespace App\Domain\Audit\Enum;

enum Etat
{
    case BROUILLON;
    case ANNULE;
    case PUBLIE;
}
