<?php

namespace App\Domain\Ventilation\Enum;

use App\Domain\Common\Enum\Enum;

enum TypeInstallation: int implements Enum
{
    case INDIVIDUEL = 1;
    case COLLECTIF = 2;

    public static function try_from_enum_type_ventilation_id(int $id): ?self
    {
        return match ($id) {
            19, 20 => self::INDIVIDUEL,
            21, 22 => self::COLLECTIF,
            default => null,
        };
    }

    public static function cases_by_type_ventilation(TypeVentilation $type_ventilation): array
    {
        return self::cases();
    }

    public static function is_applicable_by_type_ventilation(TypeVentilation $type_ventilation): bool
    {
        return $type_ventilation->ventilation_mecanique();
    }

    public static function is_requis_by_type_ventilation(TypeVentilation $type_ventilation): bool
    {
        return \in_array($type_ventilation, [
            TypeVentilation::VMC_DOUBLE_FLUX_AVEC_ECHANGEUR,
            TypeVentilation::VENTILATION_HYBRIDE,
            TypeVentilation::VENTILATION_HYBRIDE_AVEC_ENTREE_AIR_HYGROREGLABLE,
        ]);
    }

    public function id(): int
    {
        return $this->value;
    }

    public function lib(): string
    {
        return match ($this) {
            self::INDIVIDUEL => 'Individuel',
            self::COLLECTIF => 'Collectif',
        };
    }

    /**
     * Ratio du temps d'utilisation des systèmes de ventilation hybride
     */
    public function ratio_temps_utilisation(): float
    {
        return match ($this) {
            self::INDIVIDUEL => 0.083,
            self::COLLECTIF => 0.167,
        };
    }
}
