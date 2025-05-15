<?php

namespace App\Domain\Ecs\Enum;

use App\Domain\Common\Enum\Enum;

enum BouclageReseau: string implements Enum
{
    case RESEAU_NON_BOUCLE = 'non_boucle';
    case RESEAU_BOUCLE = 'boucle';
    case RESEAU_TRACE = 'trace';

    public static function from_enum_bouclage_reseau_ecs_id(int $id): ?self
    {
        return match ($id) {
            1 => self::RESEAU_NON_BOUCLE,
            2 => self::RESEAU_BOUCLE,
            3 => self::RESEAU_TRACE,
            default => null,
        };
    }

    public function id(): string
    {
        return $this->value;
    }

    public function lib(): string
    {
        return match ($this) {
            self::RESEAU_NON_BOUCLE => 'Réseau d\'ECS non bouclé',
            self::RESEAU_BOUCLE => 'Réseau d\'ECS bouclé',
            self::RESEAU_TRACE => 'Réseau d\'ECS avec présence d\'un traceur chauffant'
        };
    }
}
