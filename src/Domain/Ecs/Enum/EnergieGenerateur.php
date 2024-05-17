<?php

namespace App\Domain\Ecs\Enum;

use App\Domain\Common\Enum\{Energie, Enum};

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

    /** @return self[] */
    public static function cases_by_type_generateur(TypeGenerateur $type_generateur): array
    {
        if (\in_array($type_generateur, [
            TypeGenerateur::ACCUMULATEUR_GAZ_CLASSIQUE,
            TypeGenerateur::ACCUMULATEUR_GAZ_CONDENSATION,
            TypeGenerateur::CHAUDIERE_GAZ_CLASSIQUE,
            TypeGenerateur::CHAUDIERE_GAZ_STANDARD,
            TypeGenerateur::CHAUDIERE_GAZ_BASSE_TEMPERATURE,
            TypeGenerateur::CHAUDIERE_GAZ_CONDENSATION,
            TypeGenerateur::AUTRE_SYSTEME_COMBUSTION_GAZ,
            TypeGenerateur::POMPE_CHALEUR_HYBRIDE_PARTIE_CHAUDIERE_GAZ_CONDENSATION,
            TypeGenerateur::CHAUFFE_EAU_GAZ_PRODUCTION_INSTANTANEE,
        ])) {
            return [self::GAZ_NATUREL];
        }
        if (\in_array($type_generateur, [
            TypeGenerateur::CHAUDIERE_BOIS_BUCHE,
            TypeGenerateur::POMPE_CHALEUR_HYBRIDE_PARTIE_CHAUDIERE_BOIS_BUCHE,
        ])) {
            return [self::BOIS_BUCHES];
        }
        if (\in_array($type_generateur, [
            TypeGenerateur::CHAUDIERE_BOIS_GRANULES,
            TypeGenerateur::POMPE_CHALEUR_HYBRIDE_PARTIE_CHAUDIERE_BOIS_GRANULES,
        ])) {
            return [self::BOIS_GRANULES];
        }
        if (\in_array($type_generateur, [
            TypeGenerateur::RESEAU_CHALEUR_NON_ISOLE,
            TypeGenerateur::RESEAU_CHALEUR_ISOLE,
            TypeGenerateur::RESEAU_CHALEUR_NON_REPERTORIE_OU_INCONNU,
        ])) {
            return [self::RESEAU_CHAUFFAGE_URBAIN];
        }
        if (\in_array($type_generateur, [
            TypeGenerateur::CET_SUR_AIR_AMBIANT,
            TypeGenerateur::CET_SUR_AIR_EXTERIEUR,
            TypeGenerateur::CET_SUR_AIR_EXTRAIT,
            TypeGenerateur::PAC_DOUBLE_SERVICE,
            TypeGenerateur::AUTRE_SYSTEME_THERMODYNAMIQUE_ELECTRIQUE,
            TypeGenerateur::BALLON_ELECTRIQUE_ACCUMULATION_HORIZONTAL,
            TypeGenerateur::BALLON_ELECTRIQUE_ACCUMULATION_VERTICAL_AUTRES_OU_INCONNUE,
            TypeGenerateur::BALLON_ELECTRIQUE_ACCUMULATION_VERTICAL_CATEGORIE_B_OU_2_ETOILES,
            TypeGenerateur::BALLON_ELECTRIQUE_ACCUMULATION_VERTICAL_CATEGORIE_C_OU_3_ETOILES,
            TypeGenerateur::CHAUFFE_EAU_ELECTRIQUE_INSTANTANE,
            TypeGenerateur::CHAUDIERE_ELECTRIQUE,
            TypeGenerateur::POMPE_CHALEUR_MULTI_BATIMENT,
        ])) {
            return [self::ELECTRICITE, self::ELECTRICITE_RENOUVELABLE];
        }
        if (\in_array($type_generateur, [
            TypeGenerateur::CHAUDIERE_FIOUL_CLASSIQUE,
            TypeGenerateur::CHAUDIERE_FIOUL_STANDARD,
            TypeGenerateur::CHAUDIERE_FIOUL_BASSE_TEMPERATURE,
            TypeGenerateur::CHAUDIERE_FIOUL_CONDENSATION,
            TypeGenerateur::POMPE_CHALEUR_HYBRIDE_PARTIE_CHAUDIERE_FIOUL_CONDENSATION,
            TypeGenerateur::CHAUDIERE_FIOUL_MULTI_BATIMENT,
        ])) {
            return [self::FIOUL_DOMESTIQUE, self::AUTRES_FOSSILES];
        }
        if (\in_array($type_generateur, [
            TypeGenerateur::CHAUDIERE_GPL_PROPANE_BUTANE_CLASSIQUE,
            TypeGenerateur::CHAUDIERE_GPL_PROPANE_BUTANE_STANDARD,
            TypeGenerateur::CHAUDIERE_GPL_PROPANE_BUTANE_BASSE_TEMPERATURE,
            TypeGenerateur::CHAUDIERE_GPL_PROPANE_BUTANE_CONDENSATION,
            TypeGenerateur::ACCUMULATEUR_GPL_PROPANE_BUTANE_CLASSIQUE,
            TypeGenerateur::ACCUMULATEUR_GPL_PROPANE_BUTANE_CONDENSATION,
            TypeGenerateur::POMPE_CHALEUR_HYBRIDE_PARTIE_CHAUDIERE_GPL_PROPANE_BUTANE_CONDENSATION,
            TypeGenerateur::CHAUFFE_EAU_GPL_PROPANE_BUTANE_PRODUCTION_INSTANTANEE,
        ])) {
            return [self::PROPANE, self::BUTANE, self::GPL, self::AUTRES_FOSSILES];
        }
        return match ($type_generateur) {
            TypeGenerateur::AUTRE_SYSTEME_COMBUSTION_AUTRES_ENERGIES_FOSSILES => [self::AUTRES_FOSSILES],
            TypeGenerateur::CHAUDIERE_CHARBON_MULTI_BATIMENT => [self::CHARBON],
            TypeGenerateur::SYSTEME_COLLECTIF_DEFAUT => self::cases(),
            TypeGenerateur::CHAUDIERE_CHARBON => [self::CHARBON, self::AUTRES_FOSSILES],
            TypeGenerateur::AUTRE_SYSTEME_THERMODYNAMIQUE_GAZ, TypeGenerateur::CHAUDIERE_GAZ_MULTI_BATIMENT => [
                self::GAZ_NATUREL,
                self::PROPANE,
                self::BUTANE,
                self::GPL,
            ],
            TypeGenerateur::AUTRE_SYSTEME_COMBUSTION_BOIS, TypeGenerateur::CHAUDIERE_BOIS_MULTI_BATIMENT => [
                self::BOIS_BUCHES,
                self::BOIS_GRANULES,
                self::BOIS_PLAQUETTES_FORESTIERES,
                self::BOIS_PLAQUETTES_INDUSTRIELLES,
            ],
            TypeGenerateur::POÊLE_BOIS_BOUILLEUR_BUCHE, TypeGenerateur::POÊLE_BOIS_BOUILLEUR_GRANULES => [
                self::BOIS_BUCHES,
                self::BOIS_PLAQUETTES_FORESTIERES,
                self::BOIS_PLAQUETTES_INDUSTRIELLES,
            ],
            TypeGenerateur::CHAUDIERE_BOIS_PLAQUETTE, TypeGenerateur::POMPE_CHALEUR_HYBRIDE_PARTIE_CHAUDIERE_BOIS_PLAQUETTE => [
                self::BOIS_PLAQUETTES_FORESTIERES,
                self::BOIS_PLAQUETTES_INDUSTRIELLES,
            ],
            TypeGenerateur::AUTRE_SYSTEME_COMBUSTION_AUTRES_ENERGIES_FOSSILES => [
                self::PROPANE,
                self::BUTANE,
                self::CHARBON,
                self::GPL,
                self::AUTRES_FOSSILES,
            ],
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

    public function combustible(): float
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
