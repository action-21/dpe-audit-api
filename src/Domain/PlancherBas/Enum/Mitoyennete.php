<?php

namespace App\Domain\PlancherBas\Enum;

use App\Domain\Common\Enum\Enum;

enum Mitoyennete: int implements Enum
{
    case EXTERIEUR = 1;
    case PAROI_ENTERREE = 2;
    case VIDE_SANITAIRE = 3;
    case TERRE_PLEIN = 4;
    case SOUS_SOL_NON_CHAUFFEE = 5;
    case LOCAL_NON_CHAUFFE = 6;
    case LOCAL_TERTIAIRE_DANS_IMMEUBLE = 7;
    case BATIMENT_OU_LOCAL_HORS_HABITATION = 8;
    case LOCAL_NON_DEPERDITIF = 9;
    case LOCAL_NON_CHAUFFE_NON_ACCESSIBLE = 10;

    public static function from_type_adjacence_id(int $id): self
    {
        return match ($id) {
            1 => self::EXTERIEUR,
            2 => self::PAROI_ENTERREE,
            3 => self::VIDE_SANITAIRE,
            4 => self::BATIMENT_OU_LOCAL_HORS_HABITATION,
            5 => self::TERRE_PLEIN,
            6 => self::SOUS_SOL_NON_CHAUFFEE,
            7 => self::LOCAL_NON_CHAUFFE_NON_ACCESSIBLE,
            8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 21 => self::LOCAL_NON_CHAUFFE,
            20 => self::LOCAL_TERTIAIRE_DANS_IMMEUBLE,
            22 => self::LOCAL_NON_DEPERDITIF,
        };
    }

    public function id(): int
    {
        return $this->value;
    }

    public function lib(): string
    {
        return match ($this) {
            self::EXTERIEUR => 'Extérieur',
            self::PAROI_ENTERREE => 'Paroi enterrée',
            self::VIDE_SANITAIRE => 'Vide Sanitaire',
            self::BATIMENT_OU_LOCAL_HORS_HABITATION => 'Bâtiment ou local à usage autre que d\'habitation',
            self::TERRE_PLEIN => 'Terre-Plein',
            self::SOUS_SOL_NON_CHAUFFEE => 'Sous-sol non chauffé',
            self::LOCAL_NON_CHAUFFE => 'Local non chauffé',
            self::LOCAL_TERTIAIRE_DANS_IMMEUBLE => 'Local tertiaire à l\'intérieur de l\'immeuble en contact avec l\'appartement',
            self::LOCAL_NON_DEPERDITIF => 'Local non déperditif (local à usage d\'habitation chauffé)',
        };
    }
}
