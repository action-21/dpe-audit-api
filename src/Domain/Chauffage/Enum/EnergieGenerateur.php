<?php

namespace App\Domain\Chauffage\Enum;

use App\Domain\Common\Enum\Energie;
use App\Domain\Common\Enum\Enum;

enum EnergieGenerateur: int implements Enum
{
    case ELECTRICITE = 1;
    case GAZ_NATUREL = 2;
    case FIOUL_DOMESTIQUE = 3;
    case BOIS_BUCHES = 4;
    case BOIS_GRANULES = 5;
    case BOIS_PLAQUETTES_FORESTIERES = 6;
    case BOIS_PLAQUETTES_INDUSTRIELLES = 7;
    case RESEAU_CHAUFFAGE_URBAIN = 8;
    case PROPANE = 9;
    case BUTANE = 10;
    case CHARBON = 11;
    case ELECTRICITE_RENOUVELABLE = 12;
    case GPL = 13;
    case AUTRES_FOSSILES = 14;

    public static function from_enum_type_energie_id(int $id): self
    {
        return self::from($id);
    }

    /**
     * @return array<self>
     */
    public static function cases_by_type_generateur(TypeGenerateur $type_generateur): array
    {
        return match (true) {
            \in_array($type_generateur, [
                TypeGenerateur::CHAUDIERE_GAZ_CLASSIQUE,
                TypeGenerateur::CHAUDIERE_GAZ_STANDARD,
                TypeGenerateur::CHAUDIERE_GAZ_BASSE_TEMPERATURE,
                TypeGenerateur::CHAUDIERE_GAZ_CONDENSATION,
                TypeGenerateur::PAC_HYBRIDE_PARTIE_CHAUDIERE_GAZ_CONDENSATION,
            ]) => [
                self::GAZ_NATUREL
            ],
            \in_array($type_generateur, [
                TypeGenerateur::CHAUDIERE_BOIS_BUCHE,
                TypeGenerateur::PAC_HYBRIDE_PARTIE_CHAUDIERE_BOIS_BUCHE,
            ]) => [
                self::BOIS_BUCHES
            ],
            \in_array($type_generateur, [
                TypeGenerateur::POELE_GRANULES,
                TypeGenerateur::POELE_GRANULES_FLAMME_VERTE,
                TypeGenerateur::CHAUDIERE_BOIS_GRANULES,
                TypeGenerateur::PAC_HYBRIDE_PARTIE_CHAUDIERE_BOIS_GRANULES,
            ]) => [
                self::BOIS_GRANULES
            ],
            \in_array($type_generateur, [
                TypeGenerateur::RESEAU_CHALEUR_ISOLE,
                TypeGenerateur::RESEAU_CHALEUR_NON_ISOLE,
                TypeGenerateur::RESEAU_CHALEUR_NON_REPERTORIE_OU_INCONNU,
            ]) => [
                self::RESEAU_CHAUFFAGE_URBAIN
            ],
            \in_array($type_generateur, [
                TypeGenerateur::CHAUDIERE_CHARBON_MULTI_BATIMENT,
            ]) => [
                self::CHARBON
            ],
            \in_array($type_generateur, [
                TypeGenerateur::PAC_AIR_AIR,
                TypeGenerateur::PAC_AIR_EAU,
                TypeGenerateur::PAC_EAU_EAU,
                TypeGenerateur::PAC_EAU_GLYCOLEE_EAU,
                TypeGenerateur::PAC_GEOTHERMIQUE,
                TypeGenerateur::CONVECTEUR_ELECTRIQUE_NFC,
                TypeGenerateur::PANNEAU_RAYONNANT_ELECTRIQUE_NFC,
                TypeGenerateur::RADIATEUR_ELECTRIQUE_NFC,
                TypeGenerateur::AUTRES_EMETTEURS_EFFET_JOULE,
                TypeGenerateur::PLANCHER_OU_PLAFOND_RAYONNANT_ELECTRIQUE_AVEC_REGULATION_TERMINALE,
                TypeGenerateur::PLANCHER_OU_PLAFOND_RAYONNANT_ELECTRIQUE_SANS_REGULATION_TERMINALE,
                TypeGenerateur::RADIATEUR_ELECTRIQUE_ACCUMULATION,
                TypeGenerateur::CONVECTEUR_BI_JONCTION,
                TypeGenerateur::CHAUDIERE_ELECTRIQUE,
                TypeGenerateur::PAC_MULTI_BATIMENT,
                TypeGenerateur::AUTRE_SYSTEME_THERMODYNAMIQUE_ELECTRIQUE,
                TypeGenerateur::PAC_HYBRIDE_PARTIE_PAC,
                TypeGenerateur::PAC_HYBRIDE_PARTIE_PAC_AIR_EAU,
                TypeGenerateur::PAC_HYBRIDE_PARTIE_PAC_EAU_GLYCOLEE_EAU,
                TypeGenerateur::PAC_HYBRIDE_PARTIE_PAC_GEOTHERMIQUE,
            ]) => [
                self::ELECTRICITE,
                self::ELECTRICITE_RENOUVELABLE,
            ],
            \in_array($type_generateur, [
                TypeGenerateur::AUTRE_SYSTEME_COMBUSTION_AUTRES_ENERGIES_FOSSILES,
                TypeGenerateur::CHAUDIERE_CHARBON,
            ]) => [
                self::CHARBON,
                self::AUTRES_FOSSILES,
            ],
            \in_array($type_generateur, [
                TypeGenerateur::RADIATEUR_GAZ_INDEPENDANT_OU_AUTONOME,
                TypeGenerateur::CHAUDIERE_GAZ_MULTI_BATIMENT,
                TypeGenerateur::AUTRE_SYSTEME_COMBUSTION_GAZ,
                TypeGenerateur::AUTRE_SYSTEME_THERMODYNAMIQUE_GAZ,
            ]) => [
                self::GAZ_NATUREL,
                self::GPL,
                self::PROPANE,
                self::BUTANE,
            ],
            \in_array($type_generateur, [
                TypeGenerateur::CHAUDIERE_FIOUL_CLASSIQUE,
                TypeGenerateur::CHAUDIERE_FIOUL_STANDARD,
                TypeGenerateur::CHAUDIERE_FIOUL_BASSE_TEMPERATURE,
                TypeGenerateur::CHAUDIERE_FIOUL_CONDENSATION,
                TypeGenerateur::CHAUDIERE_FIOUL_MULTI_BATIMENT,
                TypeGenerateur::AUTRE_SYSTEME_COMBUSTION_FIOUL,
                TypeGenerateur::PAC_HYBRIDE_PARTIE_CHAUDIERE_FIOUL_CONDENSATION,
            ]) => [
                self::FIOUL_DOMESTIQUE,
                self::AUTRES_FOSSILES,
            ],
            \in_array($type_generateur, [
                TypeGenerateur::CUISINIERE,
                TypeGenerateur::INSERT,
            ]) => [
                self::FIOUL_DOMESTIQUE,
                self::BOIS_BUCHES,
                self::BOIS_GRANULES,
                self::BOIS_PLAQUETTES_FORESTIERES,
                self::BOIS_PLAQUETTES_INDUSTRIELLES,
                self::CHARBON,
                self::AUTRES_FOSSILES,
            ],
            \in_array($type_generateur, [
                TypeGenerateur::GENERATEUR_AIR_CHAUD_COMBUSTION,
                TypeGenerateur::GENERATEUR_AIR_CHAUD_COMBUSTION_CONDENSATION,
                TypeGenerateur::GENERATEUR_AIR_CHAUD_COMBUSTION_STANDARD,
                TypeGenerateur::PAC_HYBRIDE_PARTIE_CHAUDIERE,
            ]) => [
                self::FIOUL_DOMESTIQUE,
                self::BOIS_BUCHES,
                self::BOIS_GRANULES,
                self::BOIS_PLAQUETTES_FORESTIERES,
                self::BOIS_PLAQUETTES_INDUSTRIELLES,
                self::PROPANE,
                self::BUTANE,
                self::CHARBON,
                self::GPL,
                self::AUTRES_FOSSILES,
            ],
            \in_array($type_generateur, [
                TypeGenerateur::POELE_FIOUL_OU_GPL_OU_CHARBON,
            ]) => [
                self::FIOUL_DOMESTIQUE,
                self::PROPANE,
                self::BUTANE,
                self::CHARBON,
                self::GPL,
                self::AUTRES_FOSSILES,
            ],
            \in_array($type_generateur, [
                TypeGenerateur::CHAUDIERE_BOIS_MULTI_BATIMENT,
                TypeGenerateur::AUTRE_SYSTEME_COMBUSTION_BOIS,
            ]) => [
                self::BOIS_BUCHES,
                self::BOIS_GRANULES,
                self::BOIS_PLAQUETTES_FORESTIERES,
                self::BOIS_PLAQUETTES_INDUSTRIELLES,
            ],
            \in_array($type_generateur, [
                TypeGenerateur::FOYER_FERME,
            ]) => [
                self::BOIS_BUCHES,
                self::BOIS_GRANULES,
                self::BOIS_PLAQUETTES_FORESTIERES,
                self::BOIS_PLAQUETTES_INDUSTRIELLES,
                self::CHARBON,
                self::AUTRES_FOSSILES,
            ],
            \in_array($type_generateur, [
                TypeGenerateur::POELE_BOIS_BOUILLEUR_BUCHE,
            ]) => [
                self::BOIS_BUCHES,
                self::BOIS_PLAQUETTES_FORESTIERES,
                self::BOIS_PLAQUETTES_INDUSTRIELLES,
            ],
            \in_array($type_generateur, [
                TypeGenerateur::POELE_BUCHE,
            ]) => [
                self::BOIS_BUCHES,
                self::BOIS_PLAQUETTES_FORESTIERES,
                self::BOIS_PLAQUETTES_INDUSTRIELLES,
                self::CHARBON,
                self::AUTRES_FOSSILES,
            ],
            \in_array($type_generateur, [
                TypeGenerateur::POELE_BOIS_BOUILLEUR_GRANULES,
            ]) => [
                self::BOIS_GRANULES,
                self::BOIS_PLAQUETTES_FORESTIERES,
                self::BOIS_PLAQUETTES_INDUSTRIELLES,
            ],
            \in_array($type_generateur, [
                TypeGenerateur::CHAUDIERE_BOIS_PLAQUETTE,
                TypeGenerateur::PAC_HYBRIDE_PARTIE_CHAUDIERE_BOIS_PLAQUETTE,
            ]) => [
                self::BOIS_PLAQUETTES_FORESTIERES,
                self::BOIS_PLAQUETTES_INDUSTRIELLES,
            ],
            \in_array($type_generateur, [
                TypeGenerateur::CHAUDIERE_GPL_PROPANE_BUTANE_CLASSIQUE,
                TypeGenerateur::CHAUDIERE_GPL_PROPANE_BUTANE_STANDARD,
                TypeGenerateur::CHAUDIERE_GPL_PROPANE_BUTANE_BASSE_TEMPERATURE,
                TypeGenerateur::CHAUDIERE_GPL_PROPANE_BUTANE_CONDENSATION,
                TypeGenerateur::PAC_HYBRIDE_PARTIE_CHAUDIERE_GPL_PROPANE_BUTANE_CONDENSATION,
            ]) => [
                self::PROPANE,
                self::BUTANE,
                self::GPL,
                self::AUTRES_FOSSILES,
            ],
            default => self::cases(),
        };
    }

    public function id(): int
    {
        return $this->value;
    }

    public function lib(): string
    {
        return $this->energie()->lib();
    }

    public function energie(): Energie
    {
        return Energie::from($this->value);
    }

    public function combustible(): bool
    {
        return $this->energie()->combustible();
    }

    public function coefficient_conversion_pcs(): float
    {
        return $this->energie()->coefficient_conversion_pcs();
    }

    public function facteur_energie_primaire(): float
    {
        return $this->energie()->facteur_energie_primaire();
    }
}
