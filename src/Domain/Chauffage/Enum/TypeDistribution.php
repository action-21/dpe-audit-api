<?php

namespace App\Domain\Chauffage\Enum;

use App\Domain\Common\Enum\Enum;

enum TypeDistribution: int implements Enum
{
    case SANS = 1;
    case RESEAU_HYDRAULIQUE = 2;
    case RESEAU_AERAULIQUE = 3;
    case RESEAU_FLUIDE_FRIGORIGENE = 4;

    public static function try_from_enum_type_emission_distribution_id(int $id): ?self
    {
        return match ($id) {
            1, 2, 3, 4, 6, 7, 8, 9, 10, 19, 20, 21, 22, 23, 40, 50 => self::SANS,
            5, 42 => self::RESEAU_AERAULIQUE,
            11, 12, 15, 16, 24, 25, 28, 29, 32, 33, 36, 37, 46, 47 => self::RESEAU_HYDRAULIQUE,
            13, 14, 17, 18, 26, 27, 30, 31, 34, 35, 38, 39, 48, 49 => self::RESEAU_HYDRAULIQUE,
            43, 44, 45 => self::RESEAU_FLUIDE_FRIGORIGENE,
            default => null,
        };
    }

    public static function from_tv_rendement_distribution_ch_id(int $id): self
    {
        return match ($id) {
            1 => self::SANS,
            2, 7 => self::RESEAU_AERAULIQUE,
            3, 4, 8, 9 => self::RESEAU_HYDRAULIQUE,
            5, 6, 10, 11 => self::RESEAU_HYDRAULIQUE,
            12 => self::RESEAU_FLUIDE_FRIGORIGENE,
        };
    }

    /**
     * TODO: à confirmer
     * 
     * @see https://github.com/renolab/audit/discussions/21
     * 
     * @return self[]
     */
    public static function cases_by_type_generateur(TypeGenerateur $type_generateur): array
    {
        // Générateur à émission directe
        if (\in_array($type_generateur, [
            TypeGenerateur::CUISINIERE,
            TypeGenerateur::FOYER_FERME,
            TypeGenerateur::INSERT,
            TypeGenerateur::RADIATEUR_GAZ_INDEPENDANT_OU_AUTONOME,
            TypeGenerateur::CONVECTEUR_ELECTRIQUE_NFC,
            TypeGenerateur::PANNEAU_RAYONNANT_ELECTRIQUE_NFC,
            TypeGenerateur::RADIATEUR_ELECTRIQUE_NFC,
            TypeGenerateur::AUTRES_EMETTEURS_EFFET_JOULE,
            TypeGenerateur::PLANCHER_OU_PLAFOND_RAYONNANT_ELECTRIQUE_AVEC_REGULATION_TERMINALE,
            TypeGenerateur::PLANCHER_OU_PLAFOND_RAYONNANT_ELECTRIQUE_SANS_REGULATION_TERMINALE,
            TypeGenerateur::RADIATEUR_ELECTRIQUE_ACCUMULATION,
            TypeGenerateur::CONVECTEUR_BI_JONCTION,
        ])) {
            return [self::SANS];
        }
        // Générateur d'air chaud
        if (\in_array($type_generateur, [
            TypeGenerateur::PAC_AIR_AIR,
            TypeGenerateur::GENERATEUR_AIR_CHAUD_COMBUSTION,
            TypeGenerateur::GENERATEUR_AIR_CHAUD_COMBUSTION_STANDARD,
            TypeGenerateur::GENERATEUR_AIR_CHAUD_COMBUSTION_CONDENSATION,
        ])) {
            return [self::RESEAU_AERAULIQUE];
        }
        // Générateur à émission directe ou sur réseau hydraulique
        if (\in_array($type_generateur, [
            TypeGenerateur::AUTRE_SYSTEME_COMBUSTION_GAZ,
            TypeGenerateur::AUTRE_SYSTEME_COMBUSTION_FIOUL,
            TypeGenerateur::AUTRE_SYSTEME_COMBUSTION_BOIS,
            TypeGenerateur::AUTRE_SYSTEME_COMBUSTION_AUTRES_ENERGIES_FOSSILES,
        ])) {
            return [self::SANS, self::RESEAU_HYDRAULIQUE];
        }
        // Générateur à émission directe ou sur réseau aéraulique
        if (\in_array($type_generateur, [
            TypeGenerateur::POELE_BUCHE,
            TypeGenerateur::POELE_GRANULES,
            TypeGenerateur::POELE_GRANULES_FLAMME_VERTE,
            TypeGenerateur::POELE_FIOUL_OU_GPL_OU_CHARBON,
        ])) {
            return [self::SANS, self::RESEAU_AERAULIQUE];
        }
        // Réseau de chaleur
        if (\in_array($type_generateur, [
            TypeGenerateur::RESEAU_CHALEUR_NON_ISOLE,
            TypeGenerateur::RESEAU_CHALEUR_ISOLE,
            TypeGenerateur::RESEAU_CHALEUR_NON_REPERTORIE_OU_INCONNU,
        ])) {
            return [
                TypeDistribution::RESEAU_HYDRAULIQUE,
                TypeDistribution::RESEAU_AERAULIQUE,
                TypeDistribution::RESEAU_FLUIDE_FRIGORIGENE,
            ];
        }
        // Par défaut : Réseau hydraulique
        return [self::RESEAU_HYDRAULIQUE];
    }

    public function id(): int
    {
        return $this->value;
    }

    public function lib(): string
    {
        return match ($this) {
            self::SANS => 'Absence de réseau de distribution (émission directe)',
            self::RESEAU_HYDRAULIQUE => 'Réseau hydraulique',
            self::RESEAU_AERAULIQUE => 'Réseau aéraulique',
            self::RESEAU_FLUIDE_FRIGORIGENE => 'Réseau par fluide frigorigène',
        };
    }
}
