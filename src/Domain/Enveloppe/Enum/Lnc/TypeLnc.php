<?php

namespace App\Domain\Enveloppe\Enum\Lnc;

use App\Domain\Common\Enum\Enum;

enum TypeLnc: string implements Enum
{
    case GARAGE = 'garage';
    case CELLIER = 'cellier';
    case ESPACE_TAMPON_SOLARISE = 'espace_tampon_solarise';
    case COMBLE_FORTEMENT_VENTILE = 'comble_fortement_ventile';
    case COMBLE_FAIBLEMENT_VENTILE = 'comble_faiblement_ventile';
    case COMBLE_TRES_FAIBLEMENT_VENTILE = 'comble_tres_faiblement_ventile';
    case CIRCULATION_SANS_OUVERTURE_EXTERIEURE = 'circulation_sans_ouverture_exterieure';
    case CIRCULATION_AVEC_OUVERTURE_EXTERIEURE = 'circulation_avec_ouverture_exterieure';
    case CIRCULATION_AVEC_BOUCHE_OU_GAINE_DESENFUMAGE_OUVERTE = 'circulation_avec_bouche_ou_gaine_desenfumage_ouverte';
    case HALL_ENTREE_AVEC_FERMETURE_AUTOMATIQUE = 'hall_entree_avec_fermeture_automatique';
    case HALL_ENTREE_SANS_FERMETURE_AUTOMATIQUE = 'hall_entree_sans_fermeture_automatique';
    case GARAGE_COLLECTIF = 'garage_collectif';
    case AUTRES = 'autres';

    public static function from_type_adjacence_id(int $id): ?self
    {
        return match ($id) {
            8 => self::GARAGE,
            9 => self::CELLIER,
            10 => self::ESPACE_TAMPON_SOLARISE,
            11 => self::COMBLE_FORTEMENT_VENTILE,
            12 => self::COMBLE_FAIBLEMENT_VENTILE,
            13 => self::COMBLE_TRES_FAIBLEMENT_VENTILE,
            14 => self::CIRCULATION_SANS_OUVERTURE_EXTERIEURE,
            15 => self::CIRCULATION_AVEC_OUVERTURE_EXTERIEURE,
            16 => self::CIRCULATION_AVEC_BOUCHE_OU_GAINE_DESENFUMAGE_OUVERTE,
            17 => self::HALL_ENTREE_AVEC_FERMETURE_AUTOMATIQUE,
            18 => self::HALL_ENTREE_SANS_FERMETURE_AUTOMATIQUE,
            19 => self::GARAGE_COLLECTIF,
            21 => self::AUTRES,
            default => null,
        };
    }

    public function id(): string
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
            self::CIRCULATION_SANS_OUVERTURE_EXTERIEURE => 'Circulation sans ouverture directe sur l\'extérieur',
            self::CIRCULATION_AVEC_OUVERTURE_EXTERIEURE => 'Circulation avec ouverture directe sur l\'extérieur',
            self::CIRCULATION_AVEC_BOUCHE_OU_GAINE_DESENFUMAGE_OUVERTE => 'Circulation avec bouche ou gaine de désenfumage ouverte en permanence',
            self::HALL_ENTREE_AVEC_FERMETURE_AUTOMATIQUE => 'Hall d\'entrée avec dispositif de fermeture automatique',
            self::HALL_ENTREE_SANS_FERMETURE_AUTOMATIQUE => 'Hall d\'entrée sans dispositif de fermeture automatique',
            self::GARAGE_COLLECTIF => 'Garage privé collectif',
            self::AUTRES => 'Autres dépendances',
        };
    }

    public function is_ets(): bool
    {
        return $this === self::ESPACE_TAMPON_SOLARISE;
    }

    public function is_combles(): bool
    {
        return \in_array($this, [
            self::COMBLE_FORTEMENT_VENTILE,
            self::COMBLE_FAIBLEMENT_VENTILE,
            self::COMBLE_TRES_FAIBLEMENT_VENTILE
        ]);
    }
}
