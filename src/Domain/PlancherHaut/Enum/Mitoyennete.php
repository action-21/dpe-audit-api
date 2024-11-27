<?php

namespace App\Domain\PlancherHaut\Enum;

use App\Domain\Common\Enum\Enum;

enum Mitoyennete: string implements Enum
{
    case EXTERIEUR = 'EXTERIEUR';
    case ENTERRE = 'ENTERRE';
    case VIDE_SANITAIRE = 'VIDE_SANITAIRE';
    case TERRE_PLEIN = 'TERRE_PLEIN';
    case SOUS_SOL_NON_CHAUFFE = 'SOUS_SOL_NON_CHAUFFE';
    case LOCAL_NON_CHAUFFE = 'LOCAL_NON_CHAUFFE';
    case LOCAL_NON_RESIDENTIEL = 'LOCAL_NON_RESIDENTIEL';
    case LOCAL_RESIDENTIEL = 'LOCAL_RESIDENTIEL';
    case LOCAL_NON_ACCESSIBLE = 'LOCAL_NON_ACCESSIBLE';

    public static function from_type_adjacence_id(int $id): self
    {
        return match ($id) {
            1 => self::EXTERIEUR,
            2 => self::ENTERRE,
            3 => self::VIDE_SANITAIRE,
            4 => self::LOCAL_NON_RESIDENTIEL,
            5 => self::TERRE_PLEIN,
            6 => self::SOUS_SOL_NON_CHAUFFE,
            7 => self::LOCAL_NON_ACCESSIBLE,
            8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 21 => self::LOCAL_NON_CHAUFFE,
            20 => self::LOCAL_NON_RESIDENTIEL,
            22 => self::LOCAL_RESIDENTIEL,
        };
    }

    public function id(): string
    {
        return $this->value;
    }

    public function lib(): string
    {
        return match ($this) {
            self::EXTERIEUR => 'Extérieur',
            self::ENTERRE => 'Paroi enterrée',
            self::VIDE_SANITAIRE => 'Vide sanitaire',
            self::TERRE_PLEIN => 'Terre-plein',
            self::SOUS_SOL_NON_CHAUFFE => 'Sous-sol non chauffé',
            self::LOCAL_NON_CHAUFFE => 'Local non chauffé',
            self::LOCAL_NON_RESIDENTIEL => 'Bâtiment ou local à usage autre que d\'habitation',
            self::LOCAL_NON_ACCESSIBLE => 'Local non accessible',
            self::LOCAL_RESIDENTIEL => 'Local à usage d\'habitation chauffé',
        };
    }

    public function local_non_chauffe(): bool
    {
        return $this === self::LOCAL_NON_CHAUFFE;
    }
}
