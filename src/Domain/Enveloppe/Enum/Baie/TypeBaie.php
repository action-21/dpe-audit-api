<?php

namespace App\Domain\Enveloppe\Enum\Baie;

use App\Domain\Common\Enum\Enum;

enum TypeBaie: string implements Enum
{
    case BRIQUE_VERRE_PLEINE = 'brique_verre_pleine';
    case BRIQUE_VERRE_CREUSE = 'brique_verre_creuse';
    case POLYCARBONATE = 'polycarbonate';
    case FENETRE_BATTANTE = 'fenetre_battante';
    case FENETRE_COULISSANTE = 'fenetre_coulissante';
    case PORTE_FENETRE_COULISSANTE = 'porte_fenetre_coulissante';
    case PORTE_FENETRE_BATTANTE = 'porte_fenetre_battante';

    public static function from_enum_type_baie_id(int $id): self
    {
        return match ($id) {
            1 => self::BRIQUE_VERRE_PLEINE,
            2 => self::BRIQUE_VERRE_CREUSE,
            3 => self::POLYCARBONATE,
            4 => self::FENETRE_BATTANTE,
            5 => self::FENETRE_COULISSANTE,
            6 => self::PORTE_FENETRE_COULISSANTE,
            7 => self::PORTE_FENETRE_BATTANTE,
            8 => self::PORTE_FENETRE_BATTANTE,
        };
    }

    public function id(): string
    {
        return $this->value;
    }

    public function lib(): string
    {
        return match ($this) {
            self::BRIQUE_VERRE_PLEINE => 'Paroi en brique de verre pleine',
            self::BRIQUE_VERRE_CREUSE => 'Paroi en brique de verre creuse',
            self::POLYCARBONATE => 'Paroi en polycarbonnate',
            self::FENETRE_BATTANTE => 'Fenêtres battantes',
            self::FENETRE_COULISSANTE => 'Fenêtres coulissantes',
            self::PORTE_FENETRE_COULISSANTE => 'Portes-fenêtres coulissantes',
            self::PORTE_FENETRE_BATTANTE => 'Portes-fenêtres battantes sans soubassement',
        };
    }

    public function is_paroi_vitree(): bool
    {
        return \in_array($this, [self::BRIQUE_VERRE_PLEINE, self::BRIQUE_VERRE_CREUSE, self::POLYCARBONATE]);
    }

    public function is_fenetre(): bool
    {
        return \in_array($this, [self::FENETRE_BATTANTE, self::FENETRE_COULISSANTE]);
    }

    public function is_porte_fenetre(): bool
    {
        return \in_array($this, [self::PORTE_FENETRE_COULISSANTE, self::PORTE_FENETRE_BATTANTE]);
    }

    public function pont_thermique_negligeable(): bool
    {
        return \in_array($this, [self::BRIQUE_VERRE_PLEINE, self::BRIQUE_VERRE_CREUSE]);
    }
}
