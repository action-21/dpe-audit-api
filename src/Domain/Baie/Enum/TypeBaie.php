<?php

namespace App\Domain\Baie\Enum;

use App\Domain\Common\Enum\Enum;

enum TypeBaie: int implements Enum
{
    case BRIQUE_VERRE_PLEINE = 1;
    case BRIQUE_VERRE_CREUSE = 2;
    case POLYCARBONATE = 3;
    case FENETRE_BATTANTE = 4;
    case FENETRE_COULISSANTE = 5;
    case PORTE_FENETRE_COULISSANTE = 6;
    case PORTE_FENETRE_BATTANTE_SANS_SOUBASSEMENT = 7;
    case PORTE_FENETRE_BATTANTE_AVEC_SOUBASSEMENT = 8;

    public static function from_enum_type_baie_id(int $id): self
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
            self::BRIQUE_VERRE_PLEINE => 'Paroi en brique de verre pleine',
            self::BRIQUE_VERRE_CREUSE => 'Paroi en brique de verre creuse',
            self::POLYCARBONATE => 'Paroi en polycarbonnate',
            self::FENETRE_BATTANTE => 'Fenêtres battantes',
            self::FENETRE_COULISSANTE => 'Fenêtres coulissantes',
            self::PORTE_FENETRE_COULISSANTE => 'Portes-fenêtres coulissantes',
            self::PORTE_FENETRE_BATTANTE_SANS_SOUBASSEMENT => 'Portes-fenêtres battantes sans soubassement',
            self::PORTE_FENETRE_BATTANTE_AVEC_SOUBASSEMENT => 'Portes-fenêtres battantes avec soubassement'
        };
    }
}
