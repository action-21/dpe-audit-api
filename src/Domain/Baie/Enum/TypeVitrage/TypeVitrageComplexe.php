<?php

namespace App\Domain\Baie\Enum\TypeVitrage;

use App\Domain\Baie\Enum\TypeVitrage;

enum TypeVitrageComplexe: string
{
    case DOUBLE_VITRAGE = 'DOUBLE_VITRAGE';
    case DOUBLE_VITRAGE_FE = 'DOUBLE_VITRAGE_FE';
    case TRIPLE_VITRAGE = 'TRIPLE_VITRAGE';
    case TRIPLE_VITRAGE_FE = 'TRIPLE_VITRAGE_FE';

    public function to(): TypeVitrage
    {
        return TypeVitrage::from($this->value);
    }
}
