<?php

namespace App\Domain\Lnc\Enum;

use App\Domain\Common\Enum\Enum;

enum TypeBaie: string implements Enum
{
    case POLYCARBONATE = 'POLYCARBONATE';
    case FENETRE_BATTANTE = 'FENETRE_BATTANTE';
    case FENETRE_COULISSANTE = 'FENETRE_COULISSANTE';
    case PORTE_FENETRE_COULISSANTE = 'PORTE_FENETRE_COULISSANTE';
    case PORTE_FENETRE_BATTANTE = 'PORTE_FENETRE_BATTANTE';

    public static function from_tv_coef_transparence_ets_id(int $id): ?self
    {
        return match ($id) {
            1 => self::POLYCARBONATE,
            default => self::FENETRE_BATTANTE,
        };
    }

    public function id(): string
    {
        return $this->value;
    }

    public function lib(): string
    {
        return match ($this) {
            self::POLYCARBONATE => 'Paroi en polycarbonnate',
            self::FENETRE_BATTANTE => 'Fenêtres battantes',
            self::FENETRE_COULISSANTE => 'Fenêtres coulissantes',
            self::PORTE_FENETRE_COULISSANTE => 'Portes-fenêtres coulissantes',
            self::PORTE_FENETRE_BATTANTE => 'Portes-fenêtres battantes sans soubassement',
        };
    }

    public function is_fenetre(): bool
    {
        return in_array($this, [
            self::FENETRE_BATTANTE,
            self::FENETRE_COULISSANTE,
            self::PORTE_FENETRE_COULISSANTE,
            self::PORTE_FENETRE_BATTANTE,
        ]);
    }
}
