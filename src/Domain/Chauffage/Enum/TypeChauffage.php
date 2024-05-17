<?php

namespace App\Domain\Chauffage\Enum;

use App\Domain\Common\Enum\Enum;

enum TypeChauffage: int implements Enum
{
    case CHAUFFAGE_DIVISE = 1;
    case CHAUFFAGE_CENTRAL = 2;

    public static function from_enum_type_chauffage_id(int $id): self
    {
        return self::from($id);
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
        if (\in_array($type_generateur, [
            TypeGenerateur::POELE_BUCHE,
            TypeGenerateur::POELE_GRANULES,
            TypeGenerateur::POELE_GRANULES_FLAMME_VERTE,
            TypeGenerateur::POELE_FIOUL_OU_GPL_OU_CHARBON,
            TypeGenerateur::AUTRE_SYSTEME_COMBUSTION_BOIS,
            TypeGenerateur::AUTRE_SYSTEME_COMBUSTION_GAZ,
            TypeGenerateur::AUTRE_SYSTEME_COMBUSTION_FIOUL,
            TypeGenerateur::AUTRE_SYSTEME_COMBUSTION_AUTRES_ENERGIES_FOSSILES,
        ])) {
            return self::cases();
        }
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
            return [self::CHAUFFAGE_DIVISE];
        }
        return [self::CHAUFFAGE_CENTRAL];
    }

    public function id(): int
    {
        return $this->value;
    }

    public function lib(): string
    {
        return match ($this) {
            self::CHAUFFAGE_DIVISE => 'Chauffage divisé',
            self::CHAUFFAGE_CENTRAL => 'Chauffage central'
        };
    }
}
