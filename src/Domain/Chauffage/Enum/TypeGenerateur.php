<?php

namespace App\Domain\Chauffage\Enum;

use App\Domain\Common\Enum\Enum;

enum TypeGenerateur: string implements Enum
{
    case CHAUDIERE_BASSE_TEMPERATURE = 'CHAUDIERE_BASSE_TEMPERATURE';
    case CHAUDIERE_CONDENSATION = 'CHAUDIERE_CONDENSATION';
    case CHAUDIERE_MULTI_BATIMENT = 'CHAUDIERE_MULTI_BATIMENT';
    case CHAUDIERE_STANDARD = 'CHAUDIERE_STANDARD';
    case CONVECTEUR_BI_JONCTION = 'CONVECTEUR_BI_JONCTION';
    case CONVECTEUR_ELECTRIQUE = 'CONVECTEUR_ELECTRIQUE';
    case CUISINIERE = 'CUISINIERE';
    case FOYER_FERME = 'FOYER_FERME';
    case GENERATEUR_AIR_CHAUD = 'GENERATEUR_AIR_CHAUD';
    case GENERATEUR_AIR_CHAUD_CONDENSATION = 'GENERATEUR_AIR_CHAUD_CONDENSATION';
    case INSERT = 'INSERT';
    case PAC_AIR_AIR = 'PAC_AIR_AIR';
    case PAC_AIR_EAU = 'PAC_AIR_EAU';
    case PAC_EAU_EAU = 'PAC_EAU_EAU';
    case PAC_EAU_GLYCOLEE_EAU = 'PAC_EAU_GLYCOLEE_EAU';
    case PAC_GEOTHERMIQUE = 'PAC_GEOTHERMIQUE';
    case PAC_HYBRIDE_AIR_EAU = 'PAC_HYBRIDE_AIR_EAU';
    case PAC_HYBRIDE_EAU_EAU = 'PAC_HYBRIDE_EAU_EAU';
    case PAC_HYBRIDE_EAU_GLYCOLEE_EAU = 'PAC_HYBRIDE_EAU_GLYCOLEE_EAU';
    case PAC_HYBRIDE_GEOTHERMIQUE = 'PAC_HYBRIDE_GEOTHERMIQUE';
    case PAC_MULTI_BATIMENT = 'PAC_MULTI_BATIMENT';
    case PANNEAU_RAYONNANT_ELECTRIQUE = 'PANNEAU_RAYONNANT_ELECTRIQUE';
    case PLAFOND_RAYONNANT_ELECTRIQUE = 'PLAFOND_RAYONNANT_ELECTRIQUE';
    case PLANCHER_RAYONNANT_ELECTRIQUE = 'PLANCHER_RAYONNANT_ELECTRIQUE';
    case POELE = 'POELE';
    case POELE_BOUILLEUR = 'POELE_BOUILLEUR';
    case RADIATEUR_ELECTRIQUE = 'RADIATEUR_ELECTRIQUE';
    case RADIATEUR_ELECTRIQUE_ACCUMULATION = 'RADIATEUR_ELECTRIQUE_ACCUMULATION';
    case RADIATEUR_INDEPENDANT = 'RADIATEUR_INDEPENDANT';
    case RESEAU_CHALEUR = 'RESEAU_CHALEUR';
    case SYSTEME_COLLECTIF_PAR_DEFAUT = 'SYSTEME_COLLECTIF_PAR_DEFAUT';

    public static function from_type_generateur_ch_id(int $id): ?self
    {
        return match ($id) {
            81 => self::CHAUDIERE_BASSE_TEMPERATURE,
            82 => self::CHAUDIERE_BASSE_TEMPERATURE,
            91 => self::CHAUDIERE_BASSE_TEMPERATURE,
            92 => self::CHAUDIERE_BASSE_TEMPERATURE,
            93 => self::CHAUDIERE_BASSE_TEMPERATURE,
            133 => self::CHAUDIERE_BASSE_TEMPERATURE,
            134 => self::CHAUDIERE_BASSE_TEMPERATURE,
            135 => self::CHAUDIERE_BASSE_TEMPERATURE,
            83 => self::CHAUDIERE_CONDENSATION,
            84 => self::CHAUDIERE_CONDENSATION,
            94 => self::CHAUDIERE_CONDENSATION,
            95 => self::CHAUDIERE_CONDENSATION,
            96 => self::CHAUDIERE_CONDENSATION,
            97 => self::CHAUDIERE_CONDENSATION,
            136 => self::CHAUDIERE_CONDENSATION,
            137 => self::CHAUDIERE_CONDENSATION,
            138 => self::CHAUDIERE_CONDENSATION,
            139 => self::CHAUDIERE_CONDENSATION,
            148 => self::CHAUDIERE_CONDENSATION,
            149 => self::CHAUDIERE_CONDENSATION,
            150 => self::CHAUDIERE_CONDENSATION,
            151 => self::CHAUDIERE_CONDENSATION,
            160 => self::CHAUDIERE_CONDENSATION,
            161 => self::CHAUDIERE_CONDENSATION,
            109 => self::CHAUDIERE_MULTI_BATIMENT,
            110 => self::CHAUDIERE_MULTI_BATIMENT,
            111 => self::CHAUDIERE_MULTI_BATIMENT,
            171 => self::CHAUDIERE_MULTI_BATIMENT,
            55 => self::CHAUDIERE_STANDARD,
            56 => self::CHAUDIERE_STANDARD,
            57 => self::CHAUDIERE_STANDARD,
            58 => self::CHAUDIERE_STANDARD,
            59 => self::CHAUDIERE_STANDARD,
            60 => self::CHAUDIERE_STANDARD,
            61 => self::CHAUDIERE_STANDARD,
            62 => self::CHAUDIERE_STANDARD,
            63 => self::CHAUDIERE_STANDARD,
            64 => self::CHAUDIERE_STANDARD,
            65 => self::CHAUDIERE_STANDARD,
            66 => self::CHAUDIERE_STANDARD,
            67 => self::CHAUDIERE_STANDARD,
            68 => self::CHAUDIERE_STANDARD,
            69 => self::CHAUDIERE_STANDARD,
            70 => self::CHAUDIERE_STANDARD,
            71 => self::CHAUDIERE_STANDARD,
            72 => self::CHAUDIERE_STANDARD,
            73 => self::CHAUDIERE_STANDARD,
            74 => self::CHAUDIERE_STANDARD,
            75 => self::CHAUDIERE_STANDARD,
            76 => self::CHAUDIERE_STANDARD,
            77 => self::CHAUDIERE_STANDARD,
            78 => self::CHAUDIERE_STANDARD,
            79 => self::CHAUDIERE_STANDARD,
            80 => self::CHAUDIERE_STANDARD,
            85 => self::CHAUDIERE_STANDARD,
            86 => self::CHAUDIERE_STANDARD,
            87 => self::CHAUDIERE_STANDARD,
            88 => self::CHAUDIERE_STANDARD,
            89 => self::CHAUDIERE_STANDARD,
            90 => self::CHAUDIERE_STANDARD,
            106 => self::CHAUDIERE_STANDARD,
            120 => self::CHAUDIERE_STANDARD,
            121 => self::CHAUDIERE_STANDARD,
            122 => self::CHAUDIERE_STANDARD,
            123 => self::CHAUDIERE_STANDARD,
            124 => self::CHAUDIERE_STANDARD,
            125 => self::CHAUDIERE_STANDARD,
            126 => self::CHAUDIERE_STANDARD,
            127 => self::CHAUDIERE_STANDARD,
            128 => self::CHAUDIERE_STANDARD,
            129 => self::CHAUDIERE_STANDARD,
            130 => self::CHAUDIERE_STANDARD,
            131 => self::CHAUDIERE_STANDARD,
            132 => self::CHAUDIERE_STANDARD,
            152 => self::CHAUDIERE_STANDARD,
            153 => self::CHAUDIERE_STANDARD,
            154 => self::CHAUDIERE_STANDARD,
            155 => self::CHAUDIERE_STANDARD,
            156 => self::CHAUDIERE_STANDARD,
            157 => self::CHAUDIERE_STANDARD,
            158 => self::CHAUDIERE_STANDARD,
            159 => self::CHAUDIERE_STANDARD,
            105 => self::CONVECTEUR_BI_JONCTION,
            98 => self::CONVECTEUR_ELECTRIQUE,
            20 => self::CUISINIERE,
            24 => self::CUISINIERE,
            28 => self::CUISINIERE,
            32 => self::CUISINIERE,
            36 => self::CUISINIERE,
            40 => self::CUISINIERE,
            21 => self::FOYER_FERME,
            25 => self::FOYER_FERME,
            29 => self::FOYER_FERME,
            33 => self::FOYER_FERME,
            37 => self::FOYER_FERME,
            41 => self::FOYER_FERME,
            50 => self::GENERATEUR_AIR_CHAUD,
            51 => self::GENERATEUR_AIR_CHAUD,
            52 => self::GENERATEUR_AIR_CHAUD_CONDENSATION,
            23 => self::INSERT,
            27 => self::INSERT,
            31 => self::INSERT,
            35 => self::INSERT,
            39 => self::INSERT,
            43 => self::INSERT,
            1 => self::PAC_AIR_AIR,
            2 => self::PAC_AIR_AIR,
            3 => self::PAC_AIR_AIR,
            4 => self::PAC_AIR_EAU,
            5 => self::PAC_AIR_EAU,
            6 => self::PAC_AIR_EAU,
            7 => self::PAC_AIR_EAU,
            8 => self::PAC_EAU_EAU,
            9 => self::PAC_EAU_EAU,
            10 => self::PAC_EAU_EAU,
            11 => self::PAC_EAU_EAU,
            12 => self::PAC_EAU_GLYCOLEE_EAU,
            13 => self::PAC_EAU_GLYCOLEE_EAU,
            14 => self::PAC_EAU_GLYCOLEE_EAU,
            15 => self::PAC_EAU_GLYCOLEE_EAU,
            16 => self::PAC_GEOTHERMIQUE,
            17 => self::PAC_GEOTHERMIQUE,
            18 => self::PAC_GEOTHERMIQUE,
            19 => self::PAC_GEOTHERMIQUE,
            148 => self::PAC_HYBRIDE_AIR_EAU,
            149 => self::PAC_HYBRIDE_AIR_EAU,
            150 => self::PAC_HYBRIDE_AIR_EAU,
            151 => self::PAC_HYBRIDE_AIR_EAU,
            152 => self::PAC_HYBRIDE_AIR_EAU,
            153 => self::PAC_HYBRIDE_AIR_EAU,
            154 => self::PAC_HYBRIDE_AIR_EAU,
            155 => self::PAC_HYBRIDE_AIR_EAU,
            156 => self::PAC_HYBRIDE_AIR_EAU,
            157 => self::PAC_HYBRIDE_AIR_EAU,
            158 => self::PAC_HYBRIDE_AIR_EAU,
            159 => self::PAC_HYBRIDE_AIR_EAU,
            160 => self::PAC_HYBRIDE_AIR_EAU,
            161 => self::PAC_HYBRIDE_AIR_EAU,
            112 => self::PAC_MULTI_BATIMENT,
            99 => self::PANNEAU_RAYONNANT_ELECTRIQUE,
            102 => self::PLANCHER_RAYONNANT_ELECTRIQUE,
            103 => self::PLANCHER_RAYONNANT_ELECTRIQUE,
            22 => self::POELE,
            26 => self::POELE,
            30 => self::POELE,
            34 => self::POELE,
            38 => self::POELE,
            42 => self::POELE,
            44 => self::POELE,
            45 => self::POELE,
            46 => self::POELE,
            47 => self::POELE,
            48 => self::POELE_BOUILLEUR,
            49 => self::POELE_BOUILLEUR,
            140 => self::POELE_BOUILLEUR,
            141 => self::POELE_BOUILLEUR,
            100 => self::RADIATEUR_ELECTRIQUE,
            101 => self::RADIATEUR_ELECTRIQUE,
            104 => self::RADIATEUR_ELECTRIQUE_ACCUMULATION,
            53 => self::RADIATEUR_INDEPENDANT,
            54 => self::RADIATEUR_INDEPENDANT,
            107 => self::RESEAU_CHALEUR,
            108 => self::RESEAU_CHALEUR,
            142 => self::RESEAU_CHALEUR,
            119 => self::SYSTEME_COLLECTIF_PAR_DEFAUT,
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
            self::PAC_AIR_AIR => 'Pompe à chaleur air/air',
            self::PAC_AIR_EAU => 'Pompe à chaleur air/eau',
            self::PAC_EAU_EAU => 'Pompe à chaleur eau/eau',
            self::PAC_EAU_GLYCOLEE_EAU => 'Pompe à chaleur eau glycolee/eau',
            self::PAC_GEOTHERMIQUE => 'Pompe à chaleur geothermique',
            self::PAC_HYBRIDE_AIR_EAU => 'Pompe à chaleur hybride air/eau',
            self::PAC_HYBRIDE_EAU_EAU => 'Pompe à chaleur hybride eau/eau',
            self::PAC_HYBRIDE_EAU_GLYCOLEE_EAU => 'Pompe à chaleur hybride eau glycolee/eau',
            self::PAC_HYBRIDE_GEOTHERMIQUE => 'Pompe à chaleur hybride geothermique',
            self::CHAUDIERE_STANDARD => 'Chaudiere standard',
            self::CHAUDIERE_BASSE_TEMPERATURE => 'Chaudiere basse temperature',
            self::CHAUDIERE_CONDENSATION => 'Chaudiere à condensation',
            self::CHAUDIERE_MULTI_BATIMENT => 'Chaudiere multi bâtiment',
            self::PAC_MULTI_BATIMENT => 'Pompe(s) à chaleur multi bâtiment modelisee comme un reseau de chaleur',
            self::CONVECTEUR_ELECTRIQUE => 'Convecteur electrique',
            self::PANNEAU_RAYONNANT_ELECTRIQUE => 'Panneau rayonnant electrique',
            self::RADIATEUR_ELECTRIQUE => 'Radiateur electrique',
            self::PLANCHER_RAYONNANT_ELECTRIQUE => 'Plancher rayonnant electrique',
            self::PLAFOND_RAYONNANT_ELECTRIQUE => 'Plafond rayonnant electrique',
            self::RADIATEUR_ELECTRIQUE_ACCUMULATION => 'Radiateur electrique à accumulation',
            self::CONVECTEUR_BI_JONCTION => 'Convecteur bi-jonction',
            self::GENERATEUR_AIR_CHAUD => 'Generateur à air chaud',
            self::GENERATEUR_AIR_CHAUD_CONDENSATION => 'Generateur à air chaud à condensation',
            self::POELE_BOUILLEUR => 'Poêle bouilleur',
            self::CUISINIERE => 'Cuisiniere',
            self::FOYER_FERME => 'Foyer ferme',
            self::INSERT => 'Insert',
            self::POELE => 'Poêle',
            self::RADIATEUR_INDEPENDANT => 'Radiateur indépendant',
            self::RESEAU_CHALEUR => 'Reseau de chaleur',
            self::SYSTEME_COLLECTIF_PAR_DEFAUT => 'Systeme collectif par defaut en abscence d\'information : chaudiere fioul penalisante',
        };
    }

    public function usage_mixte(): bool
    {
        return \in_array($this, [
            self::CHAUDIERE_BASSE_TEMPERATURE,
            self::CHAUDIERE_CONDENSATION,
            self::CHAUDIERE_STANDARD,
            self::CHAUDIERE_MULTI_BATIMENT,
            self::SYSTEME_COLLECTIF_PAR_DEFAUT,
            self::PAC_AIR_EAU,
            self::PAC_EAU_EAU,
            self::PAC_EAU_GLYCOLEE_EAU,
            self::PAC_GEOTHERMIQUE,
            self::PAC_HYBRIDE_AIR_EAU,
            self::PAC_HYBRIDE_EAU_EAU,
            self::PAC_HYBRIDE_EAU_GLYCOLEE_EAU,
            self::PAC_HYBRIDE_GEOTHERMIQUE,
            self::PAC_MULTI_BATIMENT,
            self::POELE_BOUILLEUR,
            self::RESEAU_CHALEUR,
        ]);
    }

    public function chauffage_central(): bool
    {
        return \in_array($this, [
            self::CHAUDIERE_BASSE_TEMPERATURE,
            self::CHAUDIERE_CONDENSATION,
            self::CHAUDIERE_STANDARD,
            self::CHAUDIERE_MULTI_BATIMENT,
            self::SYSTEME_COLLECTIF_PAR_DEFAUT,
            self::GENERATEUR_AIR_CHAUD,
            self::GENERATEUR_AIR_CHAUD_CONDENSATION,
            self::PAC_AIR_AIR,
            self::PAC_AIR_EAU,
            self::PAC_EAU_EAU,
            self::PAC_EAU_GLYCOLEE_EAU,
            self::PAC_GEOTHERMIQUE,
            self::PAC_HYBRIDE_AIR_EAU,
            self::PAC_HYBRIDE_EAU_EAU,
            self::PAC_HYBRIDE_EAU_GLYCOLEE_EAU,
            self::PAC_HYBRIDE_GEOTHERMIQUE,
            self::PAC_MULTI_BATIMENT,
            self::POELE_BOUILLEUR,
            self::RESEAU_CHALEUR,
        ]);
    }

    public function chauffage_divise(): bool
    {
        return \in_array($this, [
            self::CONVECTEUR_BI_JONCTION,
            self::CONVECTEUR_ELECTRIQUE,
            self::GENERATEUR_AIR_CHAUD,
            self::PANNEAU_RAYONNANT_ELECTRIQUE,
            self::PLAFOND_RAYONNANT_ELECTRIQUE,
            self::PLANCHER_RAYONNANT_ELECTRIQUE,
            self::RADIATEUR_ELECTRIQUE,
            self::RADIATEUR_ELECTRIQUE_ACCUMULATION,
            self::PAC_AIR_AIR,
            self::CUISINIERE,
            self::FOYER_FERME,
            self::INSERT,
            self::POELE,
            self::RADIATEUR_INDEPENDANT,
        ]);
    }
}
