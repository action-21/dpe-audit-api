<?php

namespace App\Domain\Ecs\Enum;

use App\Domain\Common\Enum\Enum;

enum TypeStockage: int implements Enum
{
    case SANS_STOCKAGE = 1;
    case STOCKAGE_INDEPENDANT = 2;
    case STOCKAGE_INTEGRE = 3;

    public static function from_enum_type_stockage_ecs_id(int $id): self
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
            self::SANS_STOCKAGE => 'Abscence de stockage d\'ECS (production instantanée)',
            self::STOCKAGE_INDEPENDANT => 'Stockage indépendant de la production',
            self::STOCKAGE_INTEGRE => 'Stockage intégré à la production'
        };
    }
}
