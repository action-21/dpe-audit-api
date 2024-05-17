<?php

namespace App\Domain\Chauffage\Enum;

use App\Domain\Common\Enum\Enum;

enum TemperatureDistribution: int implements Enum
{
    /** @deprecated */
    case SANS_OBJET = 1;
    case BASSE_TEMPERATURE = 2;
    case MOYENNE_TEMPERATURE = 3;
    case HAUTE_TEMPERATURE = 4;

    public static function from_enum_temperature_distribution_id(int $id): self
    {
        return match ($id) {
            2 => self::BASSE_TEMPERATURE,
            3 => self::MOYENNE_TEMPERATURE,
            4 => self::HAUTE_TEMPERATURE,
            default => null,
        };
    }

    public static function is_applicable_by_type_distribution(TypeDistribution $type_distribution): bool
    {
        return match ($type_distribution) {
            TypeDistribution::SANS, TypeDistribution::RESEAU_AERAULIQUE => false,
            default => true,
        };
    }

    public static function is_requis_by_type_distribution(TypeDistribution $type_distribution): bool
    {
        return match ($type_distribution) {
            TypeDistribution::RESEAU_HYDRAULIQUE => true,
            default => false,
        };
    }

    public static function is_applicable_by_type_emission(TypeEmission $type_emission): bool
    {
        return match ($type_emission) {
            TypeEmission::EMETTEUR_ELECTRIQUE, TypeEmission::AIR_SOUFFLE, TypeEmission::AUTRES => false,
            default => true,
        };
    }

    /** @return self[] */
    public static function cases_by_type_emission(TypeEmission $type_emission): array
    {
        return match (true) {
            \in_array($type_emission, [
                TypeEmission::EMETTEUR_ELECTRIQUE,
                TypeEmission::AIR_SOUFFLE,
                TypeEmission::AUTRES,
            ]) => [
                self::SANS_OBJET,
            ],
            \in_array($type_emission, [
                TypeEmission::RADIATEUR_BITUBE_AVEC_ROBINET_THERMOSTATIQUE,
                TypeEmission::RADIATEUR_BITUBE_SANS_ROBINET_THERMOSTATIQUE,
                TypeEmission::RADIATEUR_MONOTUBE_AVEC_ROBINET_THERMOSTATIQUE,
                TypeEmission::RADIATEUR_MONOTUBE_SANS_ROBINET_THERMOSTATIQUE,
            ]) => [
                self::BASSE_TEMPERATURE,
                self::MOYENNE_TEMPERATURE,
                self::HAUTE_TEMPERATURE,
            ],
            default => self::cases(),
        };
    }

    public function id(): int
    {
        return $this->value;
    }

    public function lib(): string
    {
        return match ($this) {
            self::SANS_OBJET => 'Absence de réseau de distribution',
            self::BASSE_TEMPERATURE => 'Basse',
            self::MOYENNE_TEMPERATURE => 'Moyenne',
            self::HAUTE_TEMPERATURE => 'Haute'
        };
    }

    public function haute_temperature(): bool
    {
        return $this === self::HAUTE_TEMPERATURE;
    }
}
