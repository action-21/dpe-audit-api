<?php

namespace App\Domain\Mur\Enum;

use App\Domain\Common\Enum\Enum;

enum TypeMur: int implements Enum
{
    case INCONNU = 1;
    case PIERRE_MOELLONS = 2;
    case PIERRE_MOELLONS_AVEC_REMPLISSAGE = 3;
    case PISE_OU_BETON_TERRE = 4;
    case PAN_BOIS_SANS_REMPLISSAGE = 5;
    case PAN_BOIS_AVEC_REMPLISSAGE = 6;
    case BOIS_RONDIN = 7;
    case BRIQUE_PLEINE_SIMPLE = 8;
    case BRIQUE_PLEINE_DOUBLE_AVEC_LAME_AIR = 9;
    case BRIQUE_CREUSE = 10;
    case BLOC_BETON_PLEIN = 11;
    case BLOC_BETON_CREUX = 12;
    case BETON_BANCHE = 13;
    case BETON_MACHEFER = 14;
    case BRIQUE_TERRE_CUITE_ALVEOLAIRE = 15;
    case BETON_CELLULAIRE_AVANT_2013 = 16;
    case BETON_CELLULAIRE_A_PARTIR_2013 = 17;
    case OSSATURE_BOIS_AVEC_REMPLISSAGE_ISOLANT_A_PARTIR_2006 = 18;
    case SANDWICH_BETON_ISOLANT_BETON_SANS_ISOLATION_RAPPORTEE = 19;
    case CLOISON_PLATRE = 20;
    /** @deprecated */
    case AUTRES_MATERIAUX_ANCIENS = 21;
    /** @deprecated */
    case AUTRES_MATERIAUX_RECENTS = 22;
    /** @deprecated */
    case AUTRES = 23;
    case OSSATURE_BOIS_AVEC_REMPLISSAGE_ISOLANT_2001_2005 = 24;
    case OSSATURE_BOIS_SANS_REMPLISSAGE = 25;
    case OSSATURE_BOIS_AVEC_REMPLISSAGE_ISOLANT_AVANT_2001 = 26;
    case OSSATURE_BOIS_AVEC_REMPLISSAGE_TOUT_VENANT = 27;

    public static function from_enum_materiaux_structure_id(int $id): self
    {
        return static::from($id);
    }

    public function id(): int
    {
        return $this->value;
    }

    public function lib(): string
    {
        return match ($this) {
            self::INCONNU => 'Inconnu',
            self::PIERRE_MOELLONS => 'Murs en pierre de taille et moellons constitué d\'un seul matériaux',
            self::PIERRE_MOELLONS_AVEC_REMPLISSAGE => 'Murs en pierre de taille et moellons avec remplissage tout venant',
            self::PISE_OU_BETON_TERRE => 'Murs en pisé ou béton de terre stabilisé (à partir d\'argile crue)',
            self::PAN_BOIS_SANS_REMPLISSAGE => 'Murs en pan de bois sans remplissage tout venant',
            self::PAN_BOIS_AVEC_REMPLISSAGE => 'Murs en pan de bois avec remplissage tout venant',
            self::BOIS_RONDIN => 'Murs bois (rondin)',
            self::BRIQUE_PLEINE_SIMPLE => 'Murs en briques pleines simples',
            self::BRIQUE_PLEINE_DOUBLE_AVEC_LAME_AIR => 'Murs en briques pleines doubles avec lame d\'air',
            self::BRIQUE_CREUSE => 'Murs en briques creuses',
            self::BLOC_BETON_PLEIN => 'Murs en blocs de béton pleins',
            self::BLOC_BETON_CREUX => 'Murs en blocs de béton creux',
            self::BETON_BANCHE => 'Murs en béton banché',
            self::BETON_MACHEFER => 'Murs en béton de mâchefer',
            self::BRIQUE_TERRE_CUITE_ALVEOLAIRE => 'Brique terre cuite alvéolaire',
            self::BETON_CELLULAIRE_AVANT_2013 => 'Béton cellulaire avant 2013',
            self::BETON_CELLULAIRE_A_PARTIR_2013 => 'Béton cellulaire à partir de 2013',
            self::OSSATURE_BOIS_AVEC_REMPLISSAGE_ISOLANT_A_PARTIR_2006 => 'Murs en ossature bois avec isolant en remplissage ≥ 2006',
            self::SANDWICH_BETON_ISOLANT_BETON_SANS_ISOLATION_RAPPORTEE => 'Murs sandwich béton/isolant/béton (sans isolation rapportée)',
            self::CLOISON_PLATRE => 'Cloison de plâtre',
            self::AUTRES_MATERIAUX_ANCIENS => 'Autre matériau traditionel ancien',
            self::AUTRES_MATERIAUX_RECENTS => 'Autre matériau innovant récent',
            self::AUTRES => 'Autre matériau non répertorié',
            self::OSSATURE_BOIS_AVEC_REMPLISSAGE_ISOLANT_2001_2005 => 'Murs en ossature bois avec isolant en remplissage 2001-2005',
            self::OSSATURE_BOIS_SANS_REMPLISSAGE => 'Murs en ossature bois sans remplissage',
            self::OSSATURE_BOIS_AVEC_REMPLISSAGE_ISOLANT_AVANT_2001 => 'Murs en ossature bois avec isolant en remplissage <2001',
            self::OSSATURE_BOIS_AVEC_REMPLISSAGE_TOUT_VENANT => 'Murs en ossature bois avec remplissage tout venant'
        };
    }

    public function inconnu(): bool
    {
        return $this === self::INCONNU;
    }

    public function epaisseur_defaut(): int
    {
        return match ($this) {
            self::INCONNU => 0,
            self::PIERRE_MOELLONS => 20,
            self::PIERRE_MOELLONS_AVEC_REMPLISSAGE => 50,
            self::PISE_OU_BETON_TERRE => 40,
            self::PAN_BOIS_SANS_REMPLISSAGE => 8,
            self::PAN_BOIS_AVEC_REMPLISSAGE => 8,
            self::BOIS_RONDIN => 10,
            self::BRIQUE_PLEINE_SIMPLE => 9,
            self::BRIQUE_PLEINE_DOUBLE_AVEC_LAME_AIR => 20,
            self::BRIQUE_CREUSE => 15,
            self::BLOC_BETON_PLEIN => 20,
            self::BLOC_BETON_CREUX => 20,
            self::BETON_BANCHE => 20,
            self::BETON_MACHEFER => 20,
            self::BRIQUE_TERRE_CUITE_ALVEOLAIRE => 30,
            self::BETON_CELLULAIRE_AVANT_2013 => 15,
            self::BETON_CELLULAIRE_A_PARTIR_2013 => 15,
            self::OSSATURE_BOIS_AVEC_REMPLISSAGE_ISOLANT_A_PARTIR_2006 => 10,
            self::SANDWICH_BETON_ISOLANT_BETON_SANS_ISOLATION_RAPPORTEE => 15,
            self::CLOISON_PLATRE => 0,
            self::AUTRES_MATERIAUX_ANCIENS => 0,
            self::AUTRES_MATERIAUX_RECENTS => 0,
            self::AUTRES => 0,
            self::OSSATURE_BOIS_AVEC_REMPLISSAGE_ISOLANT_2001_2005 => 10,
            self::OSSATURE_BOIS_SANS_REMPLISSAGE => 8,
            self::OSSATURE_BOIS_AVEC_REMPLISSAGE_ISOLANT_AVANT_2001 => 10,
            self::OSSATURE_BOIS_AVEC_REMPLISSAGE_TOUT_VENANT => 8
        };
    }
}
