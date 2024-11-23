<?php

namespace App\Domain\Ecs\Enum;

use App\Domain\Common\Enum\Enum;

enum BouclageReseau: string implements Enum
{
    case INCONNU = 'INCONNU';
    case RESEAU_NON_BOUCLE = 'RESEAU_NON_BOUCLE';
    case RESEAU_BOUCLE = 'RESEAU_BOUCLE';
    case RESEAU_TRACE = 'RESEAU_TRACE';

    public static function from_enum_bouclage_reseau_ecs_id(int $id): self
    {
        return match ($id) {
            1 => self::RESEAU_NON_BOUCLE,
            2 => self::RESEAU_BOUCLE,
            3 => self::RESEAU_TRACE
        };
    }

    public function id(): string
    {
        return $this->value;
    }

    public function lib(): string
    {
        return match ($this) {
            self::INCONNU => 'Inconnu',
            self::RESEAU_NON_BOUCLE => 'Réseau d\'ECS non bouclé',
            self::RESEAU_BOUCLE => 'Réseau d\'ECS bouclé',
            self::RESEAU_TRACE => 'Réseau d\'ECS avec présence d\'un traceur chauffant'
        };
    }
}
