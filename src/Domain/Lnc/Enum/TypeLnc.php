<?php

namespace App\Domain\Lnc\Enum;

use App\Domain\Common\Enum\Enum;
use App\Domain\Paroi\TypeParoi;
use App\Domain\PlancherHaut\Enum\ConfigurationPlancherHaut;

/**
 * Type de local non chauffé
 */
enum TypeLnc: int implements Enum
{
    case GARAGE = 1;
    case CELLIER = 2;
    case ESPACE_TAMPON_SOLARISE = 3;
    case COMBLE_FORTEMENT_VENTILE = 4;
    case COMBLE_FAIBLEMENT_VENTILE = 5;
    case COMBLE_TRES_FAIBLEMENT_VENTILE = 6;
    case CIRCULATION_SANS_OUVERTURE_EXTERIEUR = 7;
    case CIRCULATION_AVEC_OUVERTURE_EXTERIEUR = 8;
    case CIRCULATION_AVEC_BOUCHE_OU_GAINE_DESENFUMAGE_OUVERTE = 9;
    case HALL_AVEC_FERMETURE_AUTOMATIQUE = 10;
    case HALL_SANS_FERMETURE_AUTOMATIQUE = 11;
    case GARAGE_PRIVE_COLLECTIF = 12;
    case AUTRES = 13;

    public static function scope(): string
    {
        return 'local non chauffé . type de local non chauffé';
    }

    public static function try_from_type_adjacence_id(int $id): ?self
    {
        return match ($id) {
            8 => self::GARAGE,
            9 => self::CELLIER,
            10 => self::ESPACE_TAMPON_SOLARISE,
            11 => self::COMBLE_FORTEMENT_VENTILE,
            12 => self::COMBLE_FAIBLEMENT_VENTILE,
            13 => self::COMBLE_TRES_FAIBLEMENT_VENTILE,
            14 => self::CIRCULATION_SANS_OUVERTURE_EXTERIEUR,
            15 => self::CIRCULATION_AVEC_OUVERTURE_EXTERIEUR,
            16 => self::CIRCULATION_AVEC_BOUCHE_OU_GAINE_DESENFUMAGE_OUVERTE,
            17 => self::HALL_AVEC_FERMETURE_AUTOMATIQUE,
            18 => self::HALL_SANS_FERMETURE_AUTOMATIQUE,
            19 => self::GARAGE_PRIVE_COLLECTIF,
            21 => self::AUTRES,
            default => null,
        };
    }

    public function id(): int
    {
        return $this->value;
    }

    public function lib(): string
    {
        return match ($this) {
            self::GARAGE => 'Garage',
            self::CELLIER => 'Cellier',
            self::ESPACE_TAMPON_SOLARISE => 'Espace tampon solarisé (véranda ou loggia fermée)',
            self::COMBLE_FORTEMENT_VENTILE => 'Comble fortement ventilé',
            self::COMBLE_FAIBLEMENT_VENTILE => 'Comble faiblement ventilé',
            self::COMBLE_TRES_FAIBLEMENT_VENTILE => 'Comble très faiblement ventilé',
            self::CIRCULATION_SANS_OUVERTURE_EXTERIEUR => 'Circulation sans ouverture directe sur l\'extérieur',
            self::CIRCULATION_AVEC_OUVERTURE_EXTERIEUR => 'Circulation avec ouverture directe sur l\'extérieur',
            self::CIRCULATION_AVEC_BOUCHE_OU_GAINE_DESENFUMAGE_OUVERTE => 'Circulation avec bouche ou gaine de désenfumage ouverte en permanence',
            self::HALL_AVEC_FERMETURE_AUTOMATIQUE => 'Hall d\'entrée avec dispositif de fermeture automatique',
            self::HALL_SANS_FERMETURE_AUTOMATIQUE => 'Hall d\'entrée sans dispositif de fermeture automatique',
            self::GARAGE_PRIVE_COLLECTIF => 'Garage privé collectif',
            self::AUTRES => 'Autres dépendances',
        };
    }

    public function type_paroi_applicable(TypeParoi $type_paroi): bool
    {
        return match ($this) {
            self::GARAGE, self::GARAGE_PRIVE_COLLECTIF => \in_array($type_paroi, [TypeParoi::MUR, TypeParoi::PLANCHER_BAS, TypeParoi::BAIE, TypeParoi::PORTE]),
            self::COMBLE_FORTEMENT_VENTILE, self::COMBLE_FAIBLEMENT_VENTILE, self::COMBLE_TRES_FAIBLEMENT_VENTILE => \in_array($type_paroi, [TypeParoi::MUR, TypeParoi::PLANCHER_HAUT]),
            self::HALL_AVEC_FERMETURE_AUTOMATIQUE, self::HALL_SANS_FERMETURE_AUTOMATIQUE => \in_array($type_paroi, [TypeParoi::MUR, TypeParoi::PLANCHER_BAS, TypeParoi::BAIE, TypeParoi::PORTE]),
            default => true,
        };
    }

    public function configuration_plancher_haut_applicable(ConfigurationPlancherHaut $configuration): bool
    {
        return match ($this) {
            self::COMBLE_FORTEMENT_VENTILE, self::COMBLE_FAIBLEMENT_VENTILE, self::COMBLE_TRES_FAIBLEMENT_VENTILE => $configuration === ConfigurationPlancherHaut::COMBLES_PERDUS,
            default => $this->type_paroi_applicable(TypeParoi::PLANCHER_HAUT),
        };
    }

    public function combles(): bool
    {
        return \in_array($this, [
            self::COMBLE_FORTEMENT_VENTILE,
            self::COMBLE_FAIBLEMENT_VENTILE,
            self::COMBLE_TRES_FAIBLEMENT_VENTILE
        ]);
    }
}
