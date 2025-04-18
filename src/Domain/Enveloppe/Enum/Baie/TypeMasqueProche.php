<?php

namespace App\Domain\Enveloppe\Enum\Baie;

use App\Domain\Common\Enum\Enum;

enum TypeMasqueProche: string implements Enum
{
    case FOND_BALCON_OU_FOND_ET_FLANC_LOGGIAS = 'fond_balcon_ou_fond_et_flanc_loggias';
    case BALCON_OU_AUVENT = 'balcon_ou_auvent';
    case PAROI_LATERALE_SANS_OBSTACLE_AU_SUD = 'paroi_laterale_sans_obstacle_au_sud';
    case PAROI_LATERALE_AVEC_OBSTACLE_AU_SUD = 'paroi_laterale_avec_obstacle_au_sud';

    public static function from_tv_coef_masque_proche_id(int $tv_coef_masque_proche_id): self
    {
        return match ($tv_coef_masque_proche_id) {
            1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12 => self::FOND_BALCON_OU_FOND_ET_FLANC_LOGGIAS,
            13, 14, 15, 16 => self::BALCON_OU_AUVENT,
            17 => self::PAROI_LATERALE_SANS_OBSTACLE_AU_SUD,
            18 => self::PAROI_LATERALE_AVEC_OBSTACLE_AU_SUD,
        };
    }

    public function id(): string
    {
        return $this->value;
    }

    public function lib(): string
    {
        return match ($this) {
            self::FOND_BALCON_OU_FOND_ET_FLANC_LOGGIAS => 'Baie en fond de balcon ou fond et flanc de loggias',
            self::BALCON_OU_AUVENT => 'Baie sous un balcon ou auvent',
            self::PAROI_LATERALE_SANS_OBSTACLE_AU_SUD => 'Baie masquée par une paroi latérale avec un retour qui ne fait pas obstacle au Sud',
            self::PAROI_LATERALE_AVEC_OBSTACLE_AU_SUD => 'Baie masquée par une paroi latérale avec un retour qui fait obstacle au Sud'
        };
    }

    public function avancee_applicable(): bool
    {
        return \in_array($this, [
            self::FOND_BALCON_OU_FOND_ET_FLANC_LOGGIAS,
            self::BALCON_OU_AUVENT
        ]);
    }

    public function orientation_applicable(): bool
    {
        return \in_array($this, [self::FOND_BALCON_OU_FOND_ET_FLANC_LOGGIAS]);
    }
}
