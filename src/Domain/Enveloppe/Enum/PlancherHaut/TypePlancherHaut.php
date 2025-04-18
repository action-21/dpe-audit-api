<?php

namespace App\Domain\Enveloppe\Enum\PlancherHaut;

use App\Domain\Common\Enum\Enum;

enum TypePlancherHaut: string implements Enum
{
    case PLAFOND_AVEC_OU_SANS_REMPLISSAGE = 'plafond_avec_ou_sans_remplissage';
    case PLAFOND_ENTRE_SOLIVES_METALLIQUES = 'plafond_entre_solives_metalliques';
    case PLAFOND_ENTRE_SOLIVES_BOIS = 'plafond_entre_solives_bois';
    case PLAFOND_BOIS_SUR_SOLIVES_METALLIQUES = 'plafond_bois_sur_solives_metalliques';
    case PLAFOND_BOIS_SOUS_SOLIVES_METALLIQUES = 'plafond_bois_sous_solives_metalliques';
    case BARDEAUX_ET_REMPLISSAGE = 'bardeaux_et_remplissage';
    case PLAFOND_BOIS_SUR_SOLIVES_BOIS = 'plafond_bois_sur_solives_bois';
    case PLAFOND_BOIS_SOUS_SOLIVES_BOIS = 'plafond_bois_sous_solives_bois';
    case DALLE_BETON = 'dalle_beton';
    case PLAFOND_LOURD = 'plafond_lourd';
    case COMBLES_AMENAGES_SOUS_RAMPANT = 'combles_amenages_sous_rampant';
    case TOITURE_CHAUME = 'toiture_chaume';
    case PLAFOND_PATRE = 'plafond_patre';
    case BAC_ACIER = 'bac_acier';

    public static function from_enum_type_plancher_haut_id(int $type_plancher_haut_id): ?self
    {
        return match ($type_plancher_haut_id) {
            2 => self::PLAFOND_AVEC_OU_SANS_REMPLISSAGE,
            3 => self::PLAFOND_ENTRE_SOLIVES_METALLIQUES,
            4 => self::PLAFOND_ENTRE_SOLIVES_BOIS,
            5 => self::PLAFOND_BOIS_SUR_SOLIVES_METALLIQUES,
            6 => self::PLAFOND_BOIS_SOUS_SOLIVES_METALLIQUES,
            7 => self::BARDEAUX_ET_REMPLISSAGE,
            8 => self::DALLE_BETON,
            9 => self::PLAFOND_BOIS_SUR_SOLIVES_BOIS,
            10 => self::PLAFOND_BOIS_SOUS_SOLIVES_BOIS,
            11 => self::PLAFOND_LOURD,
            12 => self::COMBLES_AMENAGES_SOUS_RAMPANT,
            13 => self::TOITURE_CHAUME,
            14 => self::PLAFOND_PATRE,
            15 => self::BAC_ACIER,
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
            self::PLAFOND_AVEC_OU_SANS_REMPLISSAGE => 'Plafond avec ou sans remplissage',
            self::PLAFOND_ENTRE_SOLIVES_METALLIQUES => 'Plafond entre solives métalliques avec ou sans remplissage',
            self::PLAFOND_ENTRE_SOLIVES_BOIS => 'Plafond entre solives bois avec ou sans remplissage',
            self::PLAFOND_BOIS_SUR_SOLIVES_METALLIQUES => 'Plafond bois sur solives métalliques',
            self::PLAFOND_BOIS_SOUS_SOLIVES_METALLIQUES => 'Plafond bois sous solives métalliques',
            self::BARDEAUX_ET_REMPLISSAGE => 'Bardeaux et remplissage',
            self::DALLE_BETON => 'Dalle béton',
            self::PLAFOND_BOIS_SUR_SOLIVES_BOIS => 'Plafond bois sur solives bois',
            self::PLAFOND_BOIS_SOUS_SOLIVES_BOIS => 'Plafond bois sous solives bois',
            self::PLAFOND_LOURD => 'Plafond lourd type entrevous terre-cuite, poutrelles béton',
            self::COMBLES_AMENAGES_SOUS_RAMPANT => 'Combles aménagés sous rampant',
            self::TOITURE_CHAUME => 'Toiture en chaume',
            self::PLAFOND_PATRE => 'Plafond en plaque de plâtre',
            self::BAC_ACIER => 'Toitures en Bac acier',
        };
    }

    public function pont_thermique_negligeable(): bool
    {
        return \in_array($this, [
            self::PLAFOND_AVEC_OU_SANS_REMPLISSAGE,
            self::PLAFOND_ENTRE_SOLIVES_METALLIQUES,
            self::PLAFOND_ENTRE_SOLIVES_BOIS,
            self::PLAFOND_BOIS_SUR_SOLIVES_METALLIQUES,
            self::PLAFOND_BOIS_SOUS_SOLIVES_METALLIQUES,
            self::BARDEAUX_ET_REMPLISSAGE,
            self::PLAFOND_BOIS_SUR_SOLIVES_BOIS,
            self::PLAFOND_BOIS_SOUS_SOLIVES_BOIS,
            self::COMBLES_AMENAGES_SOUS_RAMPANT,
            self::TOITURE_CHAUME,
            self::PLAFOND_PATRE,
        ]);
    }
}
