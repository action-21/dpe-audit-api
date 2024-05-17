<?php

namespace App\Domain\Baie\Enum;

use App\Domain\Common\Enum\Enum;

enum NatureGazLame: int implements Enum
{
    case AIR = 1;
    case ARGON_KRYPTON = 2;
    case INCONNU = 3;

    public static function from_enum_type_gaz_lame_id(int $id): self
    {
        return self::from($id);
    }

    public static function is_applicable_by_type_vitrage(TypeVitrage $type_vitrage): bool
    {
        return $type_vitrage !== TypeVitrage::SIMPLE_VITRAGE;
    }

    public static function is_requis_by_type_vitrage(TypeVitrage $type_vitrage): bool
    {
        return \in_array($type_vitrage, [
            TypeVitrage::DOUBLE_VITRAGE,
            TypeVitrage::DOUBLE_VITRAGE_FE,
            TypeVitrage::TRIPLE_VITRAGE,
            TypeVitrage::TRIPLE_VITRAGE_FE,
        ]);
    }

    public function id(): int
    {
        return $this->value;
    }

    public function lib(): string
    {
        return match ($this) {
            self::AIR => 'Air',
            self::ARGON_KRYPTON => 'Argon ou krypton',
            self::INCONNU => 'Inconnu'
        };
    }
}
