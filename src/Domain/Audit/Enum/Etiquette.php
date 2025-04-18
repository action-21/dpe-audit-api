<?php

namespace App\Domain\Audit\Enum;

use App\Domain\Common\Enum\Enum;

enum Etiquette: string implements Enum
{
    case A = 'A';
    case B = 'B';
    case C = 'C';
    case D = 'D';
    case E = 'E';
    case F = 'F';
    case G = 'G';

    public function id(): string
    {
        return $this->value;
    }

    public function lib(): string
    {
        return $this->value;
    }
}
