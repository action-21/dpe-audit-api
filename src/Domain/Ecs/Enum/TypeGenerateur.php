<?php

namespace App\Domain\Ecs\Enum;

use App\Domain\Common\Enum\Enum;

enum TypeGenerateur: string implements Enum
{
    case PAC_DOUBLE_SERVICE = 'PAC_DOUBLE_SERVICE';
    case PAC_MULTI_BATIMENT = 'PAC_MULTI_BATIMENT';
    case CHAUDIERE_STANDARD = 'CHAUDIERE_STANDARD';
    case CHAUDIERE_BASSE_TEMPERATURE = 'CHAUDIERE_BASSE_TEMPERATURE';
    case CHAUDIERE_CONDENSATION = 'CHAUDIERE_CONDENSATION';
    case CHAUDIERE_MULTI_BATIMENT = 'CHAUDIERE_MULTI_BATIMENT';
    case POELE_BOUILLEUR = 'POELE_BOUILLEUR';
    case RESEAU_CHALEUR = 'RESEAU_CHALEUR';
    case SYSTEME_COLLECTIF_PAR_DEFAUT = 'SYSTEME_COLLECTIF_PAR_DEFAUT';
    case CET_AIR_AMBIANT = 'CET_AIR_AMBIANT';
    case CET_AIR_EXTERIEUR = 'CET_AIR_EXTERIEUR';
    case CET_AIR_EXTRAIT = 'CET_AIR_EXTRAIT';
    case ACCUMULATEUR_CONDENSATION = 'ACCUMULATEUR_CONDENSATION';
    case ACCUMULATEUR_STANDARD = 'ACCUMULATEUR_STANDARD';
    case BALLON_ELECTRIQUE_HORIZONTAL = 'BALLON_ELECTRIQUE_HORIZONTAL';
    case BALLON_ELECTRIQUE_VERTICAL = 'BALLON_ELECTRIQUE_VERTICAL';
    case CHAUFFE_EAU_INSTANTANE = 'CHAUFFE_EAU_INSTANTANE';

    public static function from_enum_type_generateur_ecs_id(int $id): ?self
    {
        return match ($id) {
            1 => self::CET_AIR_AMBIANT,
            2 => self::CET_AIR_AMBIANT,
            3 => self::CET_AIR_AMBIANT,
            4 => self::CET_AIR_EXTERIEUR,
            5 => self::CET_AIR_EXTERIEUR,
            6 => self::CET_AIR_EXTERIEUR,
            7 => self::CET_AIR_EXTERIEUR,
            8 => self::CET_AIR_EXTERIEUR,
            9 => self::CET_AIR_EXTERIEUR,
            10 => self::PAC_DOUBLE_SERVICE,
            11 => self::PAC_DOUBLE_SERVICE,
            12 => self::PAC_DOUBLE_SERVICE,
            13 => self::POELE_BOUILLEUR,
            14 => self::POELE_BOUILLEUR,
            15 => self::CHAUDIERE_STANDARD,
            16 => self::CHAUDIERE_STANDARD,
            17 => self::CHAUDIERE_STANDARD,
            18 => self::CHAUDIERE_STANDARD,
            19 => self::CHAUDIERE_STANDARD,
            20 => self::CHAUDIERE_STANDARD,
            21 => self::CHAUDIERE_STANDARD,
            22 => self::CHAUDIERE_STANDARD,
            23 => self::CHAUDIERE_STANDARD,
            24 => self::CHAUDIERE_STANDARD,
            25 => self::CHAUDIERE_STANDARD,
            26 => self::CHAUDIERE_STANDARD,
            27 => self::CHAUDIERE_STANDARD,
            28 => self::CHAUDIERE_STANDARD,
            29 => self::CHAUDIERE_STANDARD,
            30 => self::CHAUDIERE_STANDARD,
            31 => self::CHAUDIERE_STANDARD,
            32 => self::CHAUDIERE_STANDARD,
            33 => self::CHAUDIERE_STANDARD,
            34 => self::CHAUDIERE_STANDARD,
            35 => self::CHAUDIERE_STANDARD,
            36 => self::CHAUDIERE_STANDARD,
            37 => self::CHAUDIERE_STANDARD,
            38 => self::CHAUDIERE_STANDARD,
            39 => self::CHAUDIERE_STANDARD,
            40 => self::CHAUDIERE_STANDARD,
            41 => self::CHAUDIERE_BASSE_TEMPERATURE,
            42 => self::CHAUDIERE_BASSE_TEMPERATURE,
            43 => self::CHAUDIERE_CONDENSATION,
            44 => self::CHAUDIERE_CONDENSATION,
            45 => self::CHAUDIERE_STANDARD,
            46 => self::CHAUDIERE_STANDARD,
            47 => self::CHAUDIERE_STANDARD,
            48 => self::CHAUDIERE_STANDARD,
            49 => self::CHAUDIERE_STANDARD,
            50 => self::CHAUDIERE_STANDARD,
            51 => self::CHAUDIERE_BASSE_TEMPERATURE,
            52 => self::CHAUDIERE_BASSE_TEMPERATURE,
            53 => self::CHAUDIERE_BASSE_TEMPERATURE,
            54 => self::CHAUDIERE_CONDENSATION,
            55 => self::CHAUDIERE_CONDENSATION,
            56 => self::CHAUDIERE_CONDENSATION,
            57 => self::CHAUDIERE_CONDENSATION,
            58 => self::CHAUDIERE_STANDARD,
            59 => self::CHAUDIERE_STANDARD,
            60 => self::CHAUDIERE_STANDARD,
            61 => self::CHAUDIERE_CONDENSATION,
            62 => self::CHAUDIERE_CONDENSATION,
            63 => self::CHAUFFE_EAU_INSTANTANE,
            64 => self::CHAUFFE_EAU_INSTANTANE,
            65 => self::CHAUFFE_EAU_INSTANTANE,
            66 => self::CHAUFFE_EAU_INSTANTANE,
            67 => self::CHAUFFE_EAU_INSTANTANE,
            68 => self::BALLON_ELECTRIQUE_HORIZONTAL,
            69 => self::BALLON_ELECTRIQUE_VERTICAL,
            70 => self::BALLON_ELECTRIQUE_VERTICAL,
            71 => self::BALLON_ELECTRIQUE_VERTICAL,
            72 => self::RESEAU_CHALEUR,
            73 => self::RESEAU_CHALEUR,
            74 => self::CHAUDIERE_MULTI_BATIMENT,
            75 => self::CHAUDIERE_MULTI_BATIMENT,
            76 => self::CHAUDIERE_MULTI_BATIMENT,
            77 => self::PAC_MULTI_BATIMENT,
            84 => self::SYSTEME_COLLECTIF_PAR_DEFAUT,
            85 => self::CHAUDIERE_STANDARD,
            86 => self::CHAUDIERE_STANDARD,
            87 => self::CHAUDIERE_STANDARD,
            88 => self::CHAUDIERE_STANDARD,
            89 => self::CHAUDIERE_STANDARD,
            90 => self::CHAUDIERE_STANDARD,
            91 => self::CHAUDIERE_STANDARD,
            92 => self::CHAUDIERE_STANDARD,
            93 => self::CHAUDIERE_STANDARD,
            94 => self::CHAUDIERE_STANDARD,
            95 => self::CHAUDIERE_STANDARD,
            96 => self::CHAUDIERE_STANDARD,
            97 => self::CHAUDIERE_STANDARD,
            98 => self::CHAUDIERE_BASSE_TEMPERATURE,
            99 => self::CHAUDIERE_BASSE_TEMPERATURE,
            100 => self::CHAUDIERE_BASSE_TEMPERATURE,
            101 => self::CHAUDIERE_CONDENSATION,
            102 => self::CHAUDIERE_CONDENSATION,
            103 => self::CHAUDIERE_CONDENSATION,
            104 => self::CHAUDIERE_CONDENSATION,
            105 => self::CHAUDIERE_STANDARD,
            106 => self::CHAUDIERE_STANDARD,
            107 => self::CHAUDIERE_STANDARD,
            108 => self::CHAUDIERE_CONDENSATION,
            109 => self::CHAUDIERE_CONDENSATION,
            110 => self::CHAUFFE_EAU_INSTANTANE,
            111 => self::CHAUFFE_EAU_INSTANTANE,
            112 => self::CHAUFFE_EAU_INSTANTANE,
            113 => self::CHAUFFE_EAU_INSTANTANE,
            114 => self::CHAUFFE_EAU_INSTANTANE,
            115 => self::POELE_BOUILLEUR,
            116 => self::POELE_BOUILLEUR,
            117 => self::CHAUFFE_EAU_INSTANTANE,
            118 => self::CHAUDIERE_STANDARD,
            119 => self::RESEAU_CHALEUR,
            120 => self::PAC_DOUBLE_SERVICE,
            121 => self::PAC_DOUBLE_SERVICE,
            122 => self::PAC_DOUBLE_SERVICE,
            123 => self::PAC_DOUBLE_SERVICE,
            124 => self::PAC_DOUBLE_SERVICE,
            125 => self::PAC_DOUBLE_SERVICE,
            126 => self::PAC_DOUBLE_SERVICE,
            127 => self::PAC_DOUBLE_SERVICE,
            128 => self::PAC_DOUBLE_SERVICE,
            129 => self::PAC_DOUBLE_SERVICE,
            130 => self::PAC_DOUBLE_SERVICE,
            131 => self::PAC_DOUBLE_SERVICE,
            132 => self::PAC_DOUBLE_SERVICE,
            133 => self::PAC_DOUBLE_SERVICE,
            134 => self::CHAUDIERE_MULTI_BATIMENT,
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
            self::PAC_DOUBLE_SERVICE => 'Pomple à chaleur double service',
            self::CHAUDIERE_STANDARD => 'Chaudiere standard',
            self::CHAUDIERE_BASSE_TEMPERATURE => 'Chaudiere basse temperature',
            self::CHAUDIERE_CONDENSATION => 'Chaudiere à condensation',
            self::CHAUDIERE_MULTI_BATIMENT => 'Chaudiere multi bâtiment',
            self::PAC_MULTI_BATIMENT => 'Pompe(s) à chaleur multi bâtiment modelisee comme un reseau de chaleur',
            self::POELE_BOUILLEUR => 'Poêle à bois bouilleur',
            self::RESEAU_CHALEUR => 'Reseau de chaleur',
            self::SYSTEME_COLLECTIF_PAR_DEFAUT => 'Systeme collectif par defaut en abscence d\'information : chaudiere fioul penalisante',
            self::CET_AIR_AMBIANT => 'Chauffe-eau thermodynamique sur air ambiant',
            self::CET_AIR_EXTERIEUR => 'Chauffe-eau thermodynamique sur air extérieur',
            self::CET_AIR_EXTRAIT => 'Chauffe-eau thermodynamique sur air extrait',
            self::ACCUMULATEUR_CONDENSATION => 'Accumulateur à condensation',
            self::ACCUMULATEUR_STANDARD => 'Accumulateur',
            self::BALLON_ELECTRIQUE_HORIZONTAL => 'Ballon électrique horizontal à accumulation',
            self::BALLON_ELECTRIQUE_VERTICAL => 'Ballon électrique vertical à accumulation',
            self::CHAUFFE_EAU_INSTANTANE => 'Chauffe-eau instantané'
        };
    }

    public function position_volume_chauffe(): ?bool
    {
        return \in_array($this, [
            self::CHAUDIERE_MULTI_BATIMENT,
            self::PAC_MULTI_BATIMENT,
        ]) ? false : null;
    }
}
