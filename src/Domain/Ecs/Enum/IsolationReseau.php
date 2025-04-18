<?php

namespace App\Domain\Ecs\Enum;

use App\Domain\Common\Enum\Enum;

enum IsolationReseau: string implements Enum
{
    case ISOLE = 'isole';
    case NON_ISOLE = 'non_isole';

    public function id(): string
    {
        return $this->value;
    }

    public function lib(): string
    {
        return match ($this) {
            self::ISOLE => 'Réseau d\'ECS isolé',
            self::NON_ISOLE => 'Réseau d\'ECS non isolé',
        };
    }
}
