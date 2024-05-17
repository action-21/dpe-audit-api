<?php

namespace App\Domain\Chauffage\Enum;

use App\Domain\Common\Enum\Enum;

enum TypeEmission: int implements Enum
{
    case EMETTEUR_ELECTRIQUE = 1;
    case AIR_SOUFFLE = 2;
    case RADIATEUR_MONOTUBE_SANS_ROBINET_THERMOSTATIQUE = 3;
    case RADIATEUR_MONOTUBE_AVEC_ROBINET_THERMOSTATIQUE = 4;
    case RADIATEUR_BITUBE_SANS_ROBINET_THERMOSTATIQUE = 5;
    case RADIATEUR_BITUBE_AVEC_ROBINET_THERMOSTATIQUE = 6;
    case AUTRES_RADIATEURS = 7;
    case PLANCHER_CHAUFFANT = 8;
    case PLAFOND_CHAUFFANT = 9;
    case AUTRES = 10;

    public static function from_enum_type_emission_distribution_id(int $id): self
    {
        return match ($id) {
            1, 2, 3, 4, 10, 40 => self::EMETTEUR_ELECTRIQUE,
            24, 25, 26, 27 => self::RADIATEUR_MONOTUBE_SANS_ROBINET_THERMOSTATIQUE,
            28, 29, 30, 31 => self::RADIATEUR_MONOTUBE_AVEC_ROBINET_THERMOSTATIQUE,
            32, 33, 34, 35 => self::RADIATEUR_BITUBE_SANS_ROBINET_THERMOSTATIQUE,
            36, 37, 38, 39 => self::RADIATEUR_BITUBE_AVEC_ROBINET_THERMOSTATIQUE,
            19, 45 => self::AUTRES_RADIATEURS,
            8, 9, 11, 12, 13, 14, 43 => self::PLANCHER_CHAUFFANT,
            6, 7, 15, 16, 17, 18, 44 => self::PLAFOND_CHAUFFANT,
            5, 42, 46, 47, 48, 49, 50 => self::AIR_SOUFFLE,
            default => self::AUTRES,
        };
    }

    /**
     * TODO: à confirmer
     * 
     * @see https://github.com/renolab/audit/discussions/21
     * 
     * @return self[]
     */
    public static function cases_by_type_distribution(TypeDistribution $type_distribution): array
    {
        return match ($type_distribution) {
            TypeDistribution::SANS => [
                self::EMETTEUR_ELECTRIQUE,
                self::AIR_SOUFFLE,
                self::AUTRES_RADIATEURS,
                self::PLANCHER_CHAUFFANT,
                self::PLAFOND_CHAUFFANT,
                self::AUTRES,
            ],
            TypeDistribution::RESEAU_HYDRAULIQUE => [
                self::RADIATEUR_MONOTUBE_SANS_ROBINET_THERMOSTATIQUE,
                self::RADIATEUR_MONOTUBE_AVEC_ROBINET_THERMOSTATIQUE,
                self::RADIATEUR_BITUBE_SANS_ROBINET_THERMOSTATIQUE,
                self::RADIATEUR_BITUBE_AVEC_ROBINET_THERMOSTATIQUE,
                self::AUTRES_RADIATEURS,
                self::PLANCHER_CHAUFFANT,
                self::PLAFOND_CHAUFFANT,
            ],
            TypeDistribution::RESEAU_AERAULIQUE => [
                self::AIR_SOUFFLE,
            ],
            TypeDistribution::RESEAU_FLUIDE_FRIGORIGENE => [
                self::AUTRES_RADIATEURS,
                self::PLANCHER_CHAUFFANT,
                self::PLAFOND_CHAUFFANT,
            ],
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
        // Émetteur électrique
        if (\in_array($type_generateur, [
            TypeGenerateur::CONVECTEUR_ELECTRIQUE_NFC,
            TypeGenerateur::PANNEAU_RAYONNANT_ELECTRIQUE_NFC,
            TypeGenerateur::RADIATEUR_ELECTRIQUE_NFC,
            TypeGenerateur::AUTRES_EMETTEURS_EFFET_JOULE,
            TypeGenerateur::RADIATEUR_ELECTRIQUE_ACCUMULATION,
            TypeGenerateur::CONVECTEUR_BI_JONCTION,
        ])) {
            return [self::EMETTEUR_ELECTRIQUE];
        }
        // Plancher ou plafond rayonnant électrique
        if (\in_array($type_generateur, [
            TypeGenerateur::PLANCHER_OU_PLAFOND_RAYONNANT_ELECTRIQUE_AVEC_REGULATION_TERMINALE,
            TypeGenerateur::PLANCHER_OU_PLAFOND_RAYONNANT_ELECTRIQUE_SANS_REGULATION_TERMINALE,
        ])) {
            return [self::PLANCHER_CHAUFFANT, self::PLAFOND_CHAUFFANT];
        }
        // Générateur d'air chaud
        if (\in_array($type_generateur, [
            TypeGenerateur::PAC_AIR_AIR,
            TypeGenerateur::GENERATEUR_AIR_CHAUD_COMBUSTION,
            TypeGenerateur::GENERATEUR_AIR_CHAUD_COMBUSTION_STANDARD,
            TypeGenerateur::GENERATEUR_AIR_CHAUD_COMBUSTION_CONDENSATION,
        ])) {
            return [self::AIR_SOUFFLE];
        }
        // Radiateur à gaz indépendant ou autonome
        if ($type_generateur === TypeGenerateur::RADIATEUR_GAZ_INDEPENDANT_OU_AUTONOME) {
            return [self::AUTRES_RADIATEURS];
        }
        // Foyer, insert ou cuisinière
        if (\in_array($type_generateur, [
            TypeGenerateur::CUISINIERE,
            TypeGenerateur::FOYER_FERME,
            TypeGenerateur::INSERT,
        ])) {
            return [self::AUTRES];
        }
        // Poêle à bois
        if (\in_array($type_generateur, [
            TypeGenerateur::POELE_BUCHE,
            TypeGenerateur::POELE_GRANULES,
            TypeGenerateur::POELE_GRANULES_FLAMME_VERTE,
            TypeGenerateur::POELE_FIOUL_OU_GPL_OU_CHARBON,
        ])) {
            return [self::AIR_SOUFFLE, self::AUTRES];
        }
        // Autres systèmes
        if (\in_array($type_generateur, [
            TypeGenerateur::AUTRE_SYSTEME_COMBUSTION_GAZ,
            TypeGenerateur::AUTRE_SYSTEME_COMBUSTION_FIOUL,
            TypeGenerateur::AUTRE_SYSTEME_COMBUSTION_BOIS,
            TypeGenerateur::AUTRE_SYSTEME_COMBUSTION_AUTRES_ENERGIES_FOSSILES,
            TypeGenerateur::AUTRE_SYSTEME_THERMODYNAMIQUE_ELECTRIQUE,
            TypeGenerateur::AUTRE_SYSTEME_THERMODYNAMIQUE_GAZ,
        ])) {
            return self::cases();
        }
        // Réseau de chaleur
        if (\in_array($type_generateur, [
            TypeGenerateur::RESEAU_CHALEUR_NON_ISOLE,
            TypeGenerateur::RESEAU_CHALEUR_ISOLE,
            TypeGenerateur::RESEAU_CHALEUR_NON_REPERTORIE_OU_INCONNU,
        ])) {
            return [
                self::AIR_SOUFFLE,
                self::RADIATEUR_MONOTUBE_SANS_ROBINET_THERMOSTATIQUE,
                self::RADIATEUR_MONOTUBE_AVEC_ROBINET_THERMOSTATIQUE,
                self::RADIATEUR_BITUBE_SANS_ROBINET_THERMOSTATIQUE,
                self::RADIATEUR_BITUBE_AVEC_ROBINET_THERMOSTATIQUE,
                self::AUTRES_RADIATEURS,
                self::PLANCHER_CHAUFFANT,
                self::PLAFOND_CHAUFFANT,
            ];
        }
        // Par défaut
        return [
            self::RADIATEUR_MONOTUBE_SANS_ROBINET_THERMOSTATIQUE,
            self::RADIATEUR_MONOTUBE_AVEC_ROBINET_THERMOSTATIQUE,
            self::RADIATEUR_BITUBE_SANS_ROBINET_THERMOSTATIQUE,
            self::RADIATEUR_BITUBE_AVEC_ROBINET_THERMOSTATIQUE,
            self::AUTRES_RADIATEURS,
            self::PLANCHER_CHAUFFANT,
            self::PLAFOND_CHAUFFANT,
        ];
    }

    public function id(): int
    {
        return $this->value;
    }

    public function lib(): string
    {
        return match ($this) {
            self::EMETTEUR_ELECTRIQUE => 'Émetteur électrique',
            self::AIR_SOUFFLE => 'Soufflage d\'air chaud',
            self::RADIATEUR_MONOTUBE_SANS_ROBINET_THERMOSTATIQUE => 'Radiateur monotube sans robinet thermostatique',
            self::RADIATEUR_MONOTUBE_AVEC_ROBINET_THERMOSTATIQUE => 'Radiateur monotube avec robinet thermostatique',
            self::RADIATEUR_BITUBE_SANS_ROBINET_THERMOSTATIQUE => 'Radiateur bitube sans robinet thermostatique',
            self::RADIATEUR_BITUBE_AVEC_ROBINET_THERMOSTATIQUE => 'Radiateur bitube avec robinet thermostatique',
            self::AUTRES_RADIATEURS => 'Autres radiateurs',
            self::PLANCHER_CHAUFFANT => 'Plancher chauffant',
            self::PLAFOND_CHAUFFANT => 'Plafond chauffant',
            self::AUTRES => 'Autres',
        };
    }

    public function categorie_id(): int
    {
        return match ($this) {
            self::EMETTEUR_ELECTRIQUE => 1,
            self::AIR_SOUFFLE => 2,
            self::RADIATEUR_MONOTUBE_SANS_ROBINET_THERMOSTATIQUE => 1,
            self::RADIATEUR_MONOTUBE_AVEC_ROBINET_THERMOSTATIQUE => 1,
            self::RADIATEUR_BITUBE_SANS_ROBINET_THERMOSTATIQUE => 1,
            self::RADIATEUR_BITUBE_AVEC_ROBINET_THERMOSTATIQUE => 1,
            self::AUTRES_RADIATEURS => 1,
            self::PLANCHER_CHAUFFANT => 3,
            self::PLAFOND_CHAUFFANT => 4,
            self::AUTRES => 2,
        };
    }

    public function categorie(): string
    {
        return match ($this) {
            self::EMETTEUR_ELECTRIQUE => "Radiateur / Convecteur",
            self::AIR_SOUFFLE => "Air soufflé",
            self::RADIATEUR_MONOTUBE_SANS_ROBINET_THERMOSTATIQUE => "Radiateur / Convecteur",
            self::RADIATEUR_MONOTUBE_AVEC_ROBINET_THERMOSTATIQUE => "Radiateur / Convecteur",
            self::RADIATEUR_BITUBE_SANS_ROBINET_THERMOSTATIQUE => "Radiateur / Convecteur",
            self::RADIATEUR_BITUBE_AVEC_ROBINET_THERMOSTATIQUE => "Radiateur / Convecteur",
            self::AUTRES_RADIATEURS => "Radiateur / Convecteur",
            self::PLANCHER_CHAUFFANT => "Plancher chauffant",
            self::PLAFOND_CHAUFFANT => "Plafond chauffant",
            self::AUTRES => "Radiateur / Convecteur",
        };
    }

    public function radiateur(): bool
    {
        return \in_array($this, [
            self::RADIATEUR_MONOTUBE_SANS_ROBINET_THERMOSTATIQUE,
            self::RADIATEUR_MONOTUBE_AVEC_ROBINET_THERMOSTATIQUE,
            self::RADIATEUR_BITUBE_SANS_ROBINET_THERMOSTATIQUE,
            self::RADIATEUR_BITUBE_AVEC_ROBINET_THERMOSTATIQUE,
            self::AUTRES_RADIATEURS,
            self::AUTRES,
        ]);
    }
}
