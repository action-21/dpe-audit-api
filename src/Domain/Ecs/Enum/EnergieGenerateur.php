<?php

namespace App\Domain\Ecs\Enum;

use App\Domain\Common\Enum\{Energie, Enum};

enum EnergieGenerateur: string implements Enum
{
    case ELECTRICITE = 'ELECTRICITE';
    case GAZ_NATUREL = 'GAZ_NATUREL';
    case GPL = 'GPL';
    case FIOUL = 'FIOUL';
    case BOIS = 'BOIS';
    case BOIS_BUCHE = 'BOIS_BUCHE';
    case BOIS_PLAQUETTE = 'BOIS_PLAQUETTE';
    case BOIS_GRANULE = 'BOIS_GRANULE';
    case CHARBON = 'CHARBON';
    case RESEAU_CHALEUR = 'RESEAU_CHALEUR';

    public static function from_enum_type_energie_id(int $id): self
    {
        return match ($id) {
            1 => self::ELECTRICITE,
            2 => self::ELECTRICITE,
            3 => self::ELECTRICITE,
            4 => self::ELECTRICITE,
            5 => self::ELECTRICITE,
            6 => self::ELECTRICITE,
            7 => self::ELECTRICITE,
            8 => self::ELECTRICITE,
            9 => self::ELECTRICITE,
            10 => self::ELECTRICITE,
            11 => self::ELECTRICITE,
            12 => self::ELECTRICITE,
            13 => self::BOIS_BUCHE,
            14 => self::BOIS_BUCHE,
            15 => self::BOIS_BUCHE,
            16 => self::BOIS_BUCHE,
            17 => self::BOIS_BUCHE,
            18 => self::BOIS_BUCHE,
            19 => self::BOIS_BUCHE,
            20 => self::BOIS_BUCHE,
            21 => self::BOIS_BUCHE,
            22 => self::BOIS_PLAQUETTE,
            23 => self::BOIS_PLAQUETTE,
            24 => self::BOIS_PLAQUETTE,
            25 => self::BOIS_PLAQUETTE,
            26 => self::BOIS_PLAQUETTE,
            27 => self::BOIS_PLAQUETTE,
            28 => self::BOIS_PLAQUETTE,
            29 => self::BOIS_GRANULE,
            30 => self::BOIS_GRANULE,
            31 => self::BOIS_GRANULE,
            32 => self::BOIS_GRANULE,
            33 => self::BOIS_GRANULE,
            34 => self::BOIS_GRANULE,
            35 => self::FIOUL,
            36 => self::FIOUL,
            37 => self::FIOUL,
            38 => self::FIOUL,
            39 => self::FIOUL,
            40 => self::FIOUL,
            41 => self::FIOUL,
            42 => self::FIOUL,
            43 => self::FIOUL,
            44 => self::FIOUL,
            45 => self::GAZ_NATUREL,
            46 => self::GAZ_NATUREL,
            47 => self::GAZ_NATUREL,
            48 => self::GAZ_NATUREL,
            49 => self::GAZ_NATUREL,
            50 => self::GAZ_NATUREL,
            51 => self::GAZ_NATUREL,
            52 => self::GAZ_NATUREL,
            53 => self::GAZ_NATUREL,
            54 => self::GAZ_NATUREL,
            55 => self::GAZ_NATUREL,
            56 => self::GAZ_NATUREL,
            57 => self::GAZ_NATUREL,
            58 => self::GAZ_NATUREL,
            59 => self::GAZ_NATUREL,
            60 => self::GAZ_NATUREL,
            61 => self::GAZ_NATUREL,
            62 => self::GAZ_NATUREL,
            63 => self::GAZ_NATUREL,
            64 => self::GAZ_NATUREL,
            65 => self::GAZ_NATUREL,
            66 => self::GAZ_NATUREL,
            67 => self::GAZ_NATUREL,
            68 => self::ELECTRICITE,
            69 => self::ELECTRICITE,
            70 => self::ELECTRICITE,
            71 => self::ELECTRICITE,
            72 => self::RESEAU_CHALEUR,
            73 => self::RESEAU_CHALEUR,
            74 => self::BOIS,
            75 => self::FIOUL,
            76 => self::GAZ_NATUREL,
            77 => self::ELECTRICITE,
            84 => self::FIOUL,
            85 => self::CHARBON,
            86 => self::CHARBON,
            87 => self::CHARBON,
            88 => self::CHARBON,
            89 => self::CHARBON,
            90 => self::CHARBON,
            91 => self::CHARBON,
            92 => self::GPL,
            93 => self::GPL,
            94 => self::GPL,
            95 => self::GPL,
            96 => self::GPL,
            97 => self::GPL,
            98 => self::GPL,
            99 => self::GPL,
            100 => self::GPL,
            101 => self::GPL,
            102 => self::GPL,
            103 => self::GPL,
            104 => self::GPL,
            105 => self::GPL,
            106 => self::GPL,
            107 => self::GPL,
            108 => self::GPL,
            109 => self::GPL,
            110 => self::GPL,
            111 => self::GPL,
            112 => self::GPL,
            113 => self::GPL,
            114 => self::GPL,
            115 => self::BOIS_GRANULE,
            116 => self::BOIS_GRANULE,
            117 => self::ELECTRICITE,
            118 => self::ELECTRICITE,
            119 => self::RESEAU_CHALEUR,
            120 => self::ELECTRICITE,
            121 => self::ELECTRICITE,
            122 => self::ELECTRICITE,
            123 => self::ELECTRICITE,
            124 => self::ELECTRICITE,
            125 => self::ELECTRICITE,
            126 => self::ELECTRICITE,
            127 => self::ELECTRICITE,
            128 => self::ELECTRICITE,
            129 => self::ELECTRICITE,
            130 => self::ELECTRICITE,
            131 => self::ELECTRICITE,
            132 => self::ELECTRICITE,
            133 => self::ELECTRICITE,
            134 => self::CHARBON,
        };
    }

    public function id(): string
    {
        return $this->value;
    }

    public function lib(): string
    {
        return match ($this) {
            self::ELECTRICITE => 'Électricité',
            self::GAZ_NATUREL => 'Gaz naturel',
            self::GPL => 'GPL',
            self::FIOUL => 'Fioul domestique',
            self::BOIS => 'Bois',
            self::BOIS_BUCHE => 'Bois - Bûches',
            self::BOIS_PLAQUETTE => 'Bois - Plaquettes',
            self::BOIS_GRANULE => 'Bois - Granulés',
            self::CHARBON => 'Charbon',
            self::RESEAU_CHALEUR => 'Réseau de chaleur',
        };
    }

    public function to(): Energie
    {
        return match ($this) {
            self::BOIS, self::BOIS_BUCHE, self::BOIS_PLAQUETTE, self::BOIS_GRANULE => Energie::BOIS,
            default => Energie::from($this->value),
        };
    }
}
