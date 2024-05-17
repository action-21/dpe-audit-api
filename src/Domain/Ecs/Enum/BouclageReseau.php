<?php

namespace App\Domain\Ecs\Enum;

use App\Domain\Common\Enum\Enum;

enum BouclageReseau: int implements Enum
{
    case RESEAU_NON_BOUCLE = 1;
    case RESEAU_BOUCLE = 2;
    case RESEAU_BOUCLE_AVEC_TRACEUR = 3;

    public static function from_enum_bouclage_reseau_ecs_id(int $id): self
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
            self::RESEAU_NON_BOUCLE => 'Réseau d\'ECS non bouclé',
            self::RESEAU_BOUCLE => 'Réseau d\'ECS bouclé',
            self::RESEAU_BOUCLE_AVEC_TRACEUR => 'Réseau d\'ECS avec présence d\'un traceur chauffant'
        };
    }
}
