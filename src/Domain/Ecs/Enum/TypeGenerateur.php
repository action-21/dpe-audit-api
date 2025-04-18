<?php

namespace App\Domain\Ecs\Enum;

use App\Domain\Common\Enum\Enum;

enum TypeGenerateur: string implements Enum
{
    case ACCUMULATEUR = 'accumulateur';
    case CHAUFFE_EAU_INSTANTANE = 'chauffe_eau_instantane';
    case CHAUFFE_EAU_VERTICAL = 'chauffe_eau_vertical';
    case CHAUFFE_EAU_HORIZONTAL = 'chauffe_eau_horizontal';
    case CHAUDIERE = 'chaudiere';
    case CET_AIR_AMBIANT = 'cet_air_ambiant';
    case CET_AIR_EXTERIEUR = 'cet_air_exterieur';
    case CET_AIR_EXTRAIT = 'cet_air_extrait';
    case PAC_DOUBLE_SERVICE = 'pac_double_service';
    case POELE_BOUILLEUR = 'poele_bouilleur';
    case RESEAU_CHALEUR = 'reseau_chaleur';

    public static function from_enum_type_generateur_ecs_id(int $id): ?self
    {
        return match ($id) {
            105, 106, 107, 108, 109 => self::ACCUMULATEUR,
            1, 2, 3 => self::CET_AIR_AMBIANT,
            4, 5, 6 => self::CET_AIR_EXTERIEUR,
            7, 8, 9 => self::CET_AIR_EXTRAIT,
            15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40,
            41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 74, 75, 76, 84, 85, 86, 87, 88, 89,
            90, 91, 92, 93, 94, 95, 96, 97, 98, 99, 100, 101, 102, 103, 104, 118, 134 => self::CHAUDIERE,
            68 => self::CHAUFFE_EAU_HORIZONTAL,
            69, 70, 71 => self::CHAUFFE_EAU_VERTICAL,
            63, 64, 65, 66, 67, 110, 111, 112, 113, 114, 117 => self::CHAUFFE_EAU_INSTANTANE,
            10, 11, 12, 77, 120, 121, 122, 123, 124, 125, 126, 127, 128, 129, 130, 131, 132, 133 => self::PAC_DOUBLE_SERVICE,
            13, 14, 115, 116 => self::POELE_BOUILLEUR,
            72, 73, 119 => self::RESEAU_CHALEUR,
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
            self::ACCUMULATEUR => 'Accumulateur',
            self::CHAUFFE_EAU_INSTANTANE => 'Chauffe-eau instantané',
            self::CHAUFFE_EAU_VERTICAL => 'Chauffe-eau vertical',
            self::CHAUFFE_EAU_HORIZONTAL => 'Chauffe-eau horizontal',
            self::CHAUDIERE => 'Chaudière',
            self::CET_AIR_AMBIANT => 'Chauffe-eau thermodynamique sur air ambiant',
            self::CET_AIR_EXTERIEUR => 'Chauffe-eau thermodynamique sur air extérieur',
            self::CET_AIR_EXTRAIT => 'Chauffe-eau thermodynamique sur air extrait',
            self::PAC_DOUBLE_SERVICE => 'Pomple à chaleur double service',
            self::POELE_BOUILLEUR => 'Poêle à bois bouilleur',
            self::RESEAU_CHALEUR => 'Réseau de chaleur',
        };
    }

    public function is_chaudiere(): bool
    {
        return \in_array($this, [
            self::CHAUDIERE,
        ]);
    }

    public function is_pac(): bool
    {
        return \in_array($this, [
            self::CET_AIR_AMBIANT,
            self::CET_AIR_EXTERIEUR,
            self::CET_AIR_EXTRAIT,
            self::PAC_DOUBLE_SERVICE,
        ]);
    }

    public function is_chauffe_eau(): bool
    {
        return \in_array($this, [
            self::ACCUMULATEUR,
            self::CHAUFFE_EAU_INSTANTANE,
            self::CHAUFFE_EAU_VERTICAL,
            self::CHAUFFE_EAU_HORIZONTAL,
        ]);
    }

    public function is_reseau_chaleur(): bool
    {
        return $this === self::RESEAU_CHALEUR;
    }

    public function is_combustion(): bool
    {
        return \in_array($this, [
            self::ACCUMULATEUR,
            self::CHAUDIERE,
            self::POELE_BOUILLEUR,
            self::CHAUFFE_EAU_INSTANTANE,
        ]);
    }
}
