<?php

namespace App\Domain\Chauffage\Enum;

use App\Domain\Batiment\Enum\TypeBatiment;
use App\Domain\Common\Enum\Enum;

enum ConfigurationInstallation: int implements Enum
{
    case SIMPLE = 1;
    case SOLAIRE = 2;
    case APPOINT_BOIS = 3;
    case BOIS_ELEC_SDB = 4;
    case APPOINT_BOIS_CHAUFFAGE_ELEC_SDB = 5;
    case CHAUDIERE_OU_PAC_RELEVE_CHAUDIERE_BOIS = 6;
    case SOLAIRE_APPOINT_BOIS = 7;
    case CHAUDIERE_RELEVE_PAC = 8;
    case CHAUDIERE_RELEVE_PAC_APPOINT_BOIS = 9;
    case COLLECTIF_BASE_APPOINT = 10;
    case CONVECTEURS = 11;

    public static function cases_by_type_batiment(TypeBatiment $type_batiment): array
    {
        return match ($type_batiment) {
            TypeBatiment::MAISON => self::cases(),
            TypeBatiment::IMMEUBLE => [
                self::SIMPLE,
                self::APPOINT_BOIS,
                self::SOLAIRE_APPOINT_BOIS,
                self::CHAUDIERE_RELEVE_PAC,
                self::CHAUDIERE_RELEVE_PAC_APPOINT_BOIS,
                self::COLLECTIF_BASE_APPOINT,
                self::CONVECTEURS,
            ]
        };
    }

    public static function from_enum_cfg_installation_ch_id(int $id): self
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
            self::SIMPLE => 'Installation de chauffage simple',
            self::SOLAIRE => 'Installation de chauffage avec chauffage solaire',
            self::APPOINT_BOIS => 'Installation de chauffage avec insert ou poêle bois en appoint',
            self::BOIS_ELEC_SDB => 'Installation de chauffage par insert, poêle bois (ou biomasse) avec un chauffage électrique dans la salle de bain',
            self::APPOINT_BOIS_CHAUFFAGE_ELEC_SDB => 'Installation de chauffage avec en appoint un insert ou poêle bois et un chauffage électrique dans la salle de bain (différent du chauffage principal)',
            self::CHAUDIERE_OU_PAC_RELEVE_CHAUDIERE_BOIS => 'Installation de chauffage avec une chaudière ou une PAC en relève d\'une chaudière bois',
            self::SOLAIRE_APPOINT_BOIS => 'Installation de chauffage avec chauffage solaire et insert ou poêle bois en appoint',
            self::CHAUDIERE_RELEVE_PAC => 'Installation de chauffage avec chaudière en relève de PAC',
            self::CHAUDIERE_RELEVE_PAC_APPOINT_BOIS => 'Installation de chauffage avec chaudière en relève de PAC avec insert ou poêle bois en appoint',
            self::COLLECTIF_BASE_APPOINT => 'Installation de chauffage collectif avec Base + appoint',
            self::CONVECTEURS => 'Convecteurs bi-jonction'
        };
    }

    public function chauffage_solaire(): bool
    {
        return match ($this) {
            self::SOLAIRE => true,
            self::SOLAIRE_APPOINT_BOIS => true,
            default => false
        };
    }

    public function generateurs(): int
    {
        return match ($this) {
            self::SIMPLE, self::SOLAIRE, self::CONVECTEURS => 1,
            self::APPOINT_BOIS, self::BOIS_ELEC_SDB, self::CHAUDIERE_OU_PAC_RELEVE_CHAUDIERE_BOIS, self::SOLAIRE_APPOINT_BOIS => 2,
            self::APPOINT_BOIS_CHAUFFAGE_ELEC_SDB, self::CHAUDIERE_RELEVE_PAC_APPOINT_BOIS => 3,
        };
    }
}
