<?php

namespace App\Domain\Porte\Enum;

use App\Domain\Common\Enum\Enum;

enum TypePorte: int implements Enum
{
    case PORTE_OPAQUE_PLEINE = 1;
    case PORTE_AVEC_VITRAGE_SIMPLE = 2;
    case PORTE_AVEC_VITRAGE_SIMPLE_LT30 = 3;
    case PORTE_AVEC_VITRAGE_SIMPLE_LTE60 = 4;
    case PORTE_AVEC_DOUBLE_VITRAGE = 5;
    case PORTE_AVEC_DOUBLE_VITRAGE_LT30 = 6;
    case PORTE_AVEC_DOUBLE_VITRAGE_LTE60 = 7;
    case PORTE_ISOLEE_AVEC_DOUBLE_VITRAGE = 8;
    case PORTE_OPAQUE_PLEINE_ISOLEE = 9;
    case PORTE_AVEC_SAS = 10;
    /** @deprecated */
    case AUTRES = 11;

    public static function from_enum_type_porte_id(int $id): self
    {
        return match ($id) {
            1, 5, 9 => self::PORTE_OPAQUE_PLEINE,
            2, 6 => self::PORTE_AVEC_VITRAGE_SIMPLE_LT30,
            3, 7 => self::PORTE_AVEC_VITRAGE_SIMPLE_LTE60,
            4, 8 => self::PORTE_AVEC_DOUBLE_VITRAGE,
            5 => self::PORTE_AVEC_DOUBLE_VITRAGE_LT30,
            10 => self::PORTE_AVEC_VITRAGE_SIMPLE,
            11 => self::PORTE_AVEC_DOUBLE_VITRAGE_LT30,
            12 => self::PORTE_AVEC_DOUBLE_VITRAGE_LTE60,
            13 => self::PORTE_OPAQUE_PLEINE_ISOLEE,
            14 => self::PORTE_AVEC_SAS,
            15 => self::PORTE_ISOLEE_AVEC_DOUBLE_VITRAGE,
            16 => self::AUTRES,
        };
    }

    /** @return array<self> */
    public static function cases_by_nature_menuiserie(NatureMenuiserie $nature_menuiserie): array
    {
        return match ($nature_menuiserie) {
            NatureMenuiserie::PORTE_SIMPLE_BOIS => [
                self::PORTE_OPAQUE_PLEINE,
                self::PORTE_AVEC_VITRAGE_SIMPLE_LT30,
                self::PORTE_AVEC_VITRAGE_SIMPLE_LTE60,
                self::PORTE_AVEC_DOUBLE_VITRAGE,
            ],
            NatureMenuiserie::PORTE_SIMPLE_PVC => [
                self::PORTE_OPAQUE_PLEINE,
                self::PORTE_AVEC_VITRAGE_SIMPLE_LT30,
                self::PORTE_AVEC_VITRAGE_SIMPLE_LTE60,
                self::PORTE_AVEC_DOUBLE_VITRAGE,
            ],
            NatureMenuiserie::PORTE_SIMPLE_METAL => [
                self::PORTE_OPAQUE_PLEINE,
                self::PORTE_AVEC_VITRAGE_SIMPLE,
                self::PORTE_AVEC_DOUBLE_VITRAGE_LT30,
                self::PORTE_AVEC_DOUBLE_VITRAGE_LTE60,
            ],
            NatureMenuiserie::AUTRES => [
                self::PORTE_ISOLEE_AVEC_DOUBLE_VITRAGE,
                self::PORTE_OPAQUE_PLEINE_ISOLEE,
                self::PORTE_AVEC_SAS,
                self::AUTRES,
            ],
        };
    }

    /** @inheritdoc */
    public function id(): int
    {
        return $this->value;
    }

    /** @inheritdoc */
    public function lib(): string
    {
        return match ($this) {
            self::PORTE_OPAQUE_PLEINE => 'Porte opaque pleine',
            self::PORTE_AVEC_VITRAGE_SIMPLE => 'Porte avec vitrage simple',
            self::PORTE_AVEC_VITRAGE_SIMPLE_LT30 => 'Porte avec moins de 30% de vitrage simple',
            self::PORTE_AVEC_VITRAGE_SIMPLE_LTE60 => 'Porte avec 30-60% de vitrage simple',
            self::PORTE_AVEC_DOUBLE_VITRAGE => 'Porte avec double vitrage',
            self::PORTE_AVEC_DOUBLE_VITRAGE_LT30 => 'Porte avec moins de 30% de double vitrage',
            self::PORTE_AVEC_DOUBLE_VITRAGE_LTE60 => 'Porte avec 30-60% de double vitrage',
            self::PORTE_ISOLEE_AVEC_DOUBLE_VITRAGE => 'Porte isolée avec double vitrage',
            self::PORTE_OPAQUE_PLEINE_ISOLEE => 'Porte opaque pleine isolée',
            self::PORTE_AVEC_SAS => 'Porte précédée d\'un SAS',
            self::AUTRES => 'Autres types de porte',
        };
    }

    public function est_isole(): bool
    {
        return false;
    }

    /** @return NatureMenuiserie[] */
    public function nature_menuiserie_applicable(): array
    {
        return match ($this) {
            self::PORTE_OPAQUE_PLEINE => [
                NatureMenuiserie::PORTE_SIMPLE_BOIS,
                NatureMenuiserie::PORTE_SIMPLE_PVC,
                NatureMenuiserie::PORTE_SIMPLE_METAL,
            ],
            self::PORTE_AVEC_VITRAGE_SIMPLE, self::PORTE_AVEC_DOUBLE_VITRAGE_LT30, self::PORTE_AVEC_DOUBLE_VITRAGE_LTE60 => [
                NatureMenuiserie::PORTE_SIMPLE_METAL,
            ],
            self::PORTE_AVEC_VITRAGE_SIMPLE_LT30, self::PORTE_AVEC_VITRAGE_SIMPLE_LTE60, self::PORTE_AVEC_DOUBLE_VITRAGE => [
                NatureMenuiserie::PORTE_SIMPLE_BOIS,
                NatureMenuiserie::PORTE_SIMPLE_PVC,
            ],
            self::PORTE_ISOLEE_AVEC_DOUBLE_VITRAGE, self::PORTE_OPAQUE_PLEINE_ISOLEE, self::PORTE_AVEC_SAS, self::AUTRES => [
                NatureMenuiserie::AUTRES,
            ],
        };
    }
}
