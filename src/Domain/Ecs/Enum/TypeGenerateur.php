<?php

namespace App\Domain\Ecs\Enum;

use App\Domain\Common\Enum\Enum;

enum TypeGenerateur: int implements Enum
{
    case CET_SUR_AIR_AMBIANT = 1;
    case CET_SUR_AIR_EXTERIEUR = 2;
    case CET_SUR_AIR_EXTRAIT = 3;
    case PAC_DOUBLE_SERVICE = 4;
    case POÊLE_BOIS_BOUILLEUR_BUCHE = 5;
    case CHAUDIERE_BOIS_BUCHE = 6;
    case CHAUDIERE_BOIS_PLAQUETTE = 7;
    case CHAUDIERE_BOIS_GRANULES = 8;
    case CHAUDIERE_FIOUL_CLASSIQUE = 9;
    case CHAUDIERE_FIOUL_STANDARD = 10;
    case CHAUDIERE_FIOUL_BASSE_TEMPERATURE = 11;
    case CHAUDIERE_FIOUL_CONDENSATION = 12;
    case CHAUDIERE_GAZ_CLASSIQUE = 13;
    case CHAUDIERE_GAZ_STANDARD = 14;
    case CHAUDIERE_GAZ_BASSE_TEMPERATURE = 15;
    case CHAUDIERE_GAZ_CONDENSATION = 16;
    case ACCUMULATEUR_GAZ_CLASSIQUE = 17;
    case ACCUMULATEUR_GAZ_CONDENSATION = 18;
    case CHAUFFE_EAU_GAZ_PRODUCTION_INSTANTANEE = 19;
    case BALLON_ELECTRIQUE_ACCUMULATION_HORIZONTAL = 20;
    case BALLON_ELECTRIQUE_ACCUMULATION_VERTICAL_AUTRES_OU_INCONNUE = 21;
    case BALLON_ELECTRIQUE_ACCUMULATION_VERTICAL_CATEGORIE_B_OU_2_ETOILES = 22;
    case BALLON_ELECTRIQUE_ACCUMULATION_VERTICAL_CATEGORIE_C_OU_3_ETOILES = 23;
    case RESEAU_CHALEUR_NON_ISOLE = 24;
    case RESEAU_CHALEUR_ISOLE = 25;
    case CHAUDIERE_BOIS_MULTI_BATIMENT = 26;
    case CHAUDIERE_FIOUL_MULTI_BATIMENT = 27;
    case CHAUDIERE_GAZ_MULTI_BATIMENT = 28;
    case POMPE_CHALEUR_MULTI_BATIMENT = 29;
    /** @deprecated */
    case AUTRE_SYSTEME_COMBUSTION_GAZ = 30;
    /** @deprecated */
    case AUTRE_SYSTEME_COMBUSTION_FIOUL = 31;
    /** @deprecated */
    case AUTRE_SYSTEME_COMBUSTION_BOIS = 32;
    /** @deprecated */
    case AUTRE_SYSTEME_COMBUSTION_AUTRES_ENERGIES_FOSSILES = 33;
    /** @deprecated */
    case AUTRE_SYSTEME_THERMODYNAMIQUE_ELECTRIQUE = 34;
    /** @deprecated */
    case AUTRE_SYSTEME_THERMODYNAMIQUE_GAZ = 35;
    case SYSTEME_COLLECTIF_DEFAUT = 36;
    case CHAUDIERE_CHARBON = 37;
    case CHAUDIERE_GPL_PROPANE_BUTANE_CLASSIQUE = 38;
    case CHAUDIERE_GPL_PROPANE_BUTANE_STANDARD = 39;
    case CHAUDIERE_GPL_PROPANE_BUTANE_BASSE_TEMPERATURE = 40;
    case CHAUDIERE_GPL_PROPANE_BUTANE_CONDENSATION = 41;
    case ACCUMULATEUR_GPL_PROPANE_BUTANE_CLASSIQUE = 42;
    case ACCUMULATEUR_GPL_PROPANE_BUTANE_CONDENSATION = 43;
    case CHAUFFE_EAU_GPL_PROPANE_BUTANE_PRODUCTION_INSTANTANEE = 44;
    case POÊLE_BOIS_BOUILLEUR_GRANULES = 45;
    case CHAUFFE_EAU_ELECTRIQUE_INSTANTANE = 46;
    case CHAUDIERE_ELECTRIQUE = 47;
    case RESEAU_CHALEUR_NON_REPERTORIE_OU_INCONNU = 48;
    case POMPE_CHALEUR_HYBRIDE_PARTIE_CHAUDIERE_GAZ_CONDENSATION = 49;
    case POMPE_CHALEUR_HYBRIDE_PARTIE_CHAUDIERE_FIOUL_CONDENSATION = 50;
    case POMPE_CHALEUR_HYBRIDE_PARTIE_CHAUDIERE_BOIS_GRANULES = 51;
    case POMPE_CHALEUR_HYBRIDE_PARTIE_CHAUDIERE_BOIS_BUCHE = 52;
    case POMPE_CHALEUR_HYBRIDE_PARTIE_CHAUDIERE_BOIS_PLAQUETTE = 53;
    case POMPE_CHALEUR_HYBRIDE_PARTIE_CHAUDIERE_GPL_PROPANE_BUTANE_CONDENSATION = 54;
    case CHAUDIERE_CHARBON_MULTI_BATIMENT = 55;

    public static function from_enum_type_generateur_ecs_id(int $id): self
    {
        return match ($id) {
            1, 2, 3 => self::CET_SUR_AIR_AMBIANT,
            4, 5, 6 => self::CET_SUR_AIR_EXTERIEUR,
            7, 8, 9 => self::CET_SUR_AIR_EXTRAIT,
            10, 11, 12 => self::PAC_DOUBLE_SERVICE,
            13, 14 => self::POÊLE_BOIS_BOUILLEUR_BUCHE,
            15, 16, 17, 18, 19, 20, 21 => self::CHAUDIERE_BOIS_BUCHE,
            22, 23, 24, 25, 26, 27, 28 => self::CHAUDIERE_BOIS_PLAQUETTE,
            29, 30, 31, 32, 33, 34 => self::CHAUDIERE_BOIS_GRANULES,
            35, 36, 37, 38 => self::CHAUDIERE_FIOUL_CLASSIQUE,
            39, 40 => self::CHAUDIERE_FIOUL_STANDARD,
            41, 42 => self::CHAUDIERE_FIOUL_BASSE_TEMPERATURE,
            43, 44 => self::CHAUDIERE_FIOUL_CONDENSATION,
            45, 46, 47 => self::CHAUDIERE_GAZ_CLASSIQUE,
            48, 49, 50 => self::CHAUDIERE_GAZ_STANDARD,
            51, 52, 53 => self::CHAUDIERE_GAZ_BASSE_TEMPERATURE,
            54, 55, 56, 57 => self::CHAUDIERE_GAZ_CONDENSATION,
            58, 59, 60 => self::ACCUMULATEUR_GAZ_CLASSIQUE,
            61, 62 => self::ACCUMULATEUR_GAZ_CONDENSATION,
            63, 64, 65, 66, 67 => self::CHAUFFE_EAU_GAZ_PRODUCTION_INSTANTANEE,
            68 => self::BALLON_ELECTRIQUE_ACCUMULATION_HORIZONTAL,
            69 => self::BALLON_ELECTRIQUE_ACCUMULATION_VERTICAL_AUTRES_OU_INCONNUE,
            70 => self::BALLON_ELECTRIQUE_ACCUMULATION_VERTICAL_CATEGORIE_B_OU_2_ETOILES,
            71 => self::BALLON_ELECTRIQUE_ACCUMULATION_VERTICAL_CATEGORIE_C_OU_3_ETOILES,
            72 => self::RESEAU_CHALEUR_NON_ISOLE,
            73 => self::RESEAU_CHALEUR_ISOLE,
            74 => self::CHAUDIERE_BOIS_MULTI_BATIMENT,
            75 => self::CHAUDIERE_FIOUL_MULTI_BATIMENT,
            76 => self::CHAUDIERE_GAZ_MULTI_BATIMENT,
            77 => self::POMPE_CHALEUR_MULTI_BATIMENT,
            78 => self::AUTRE_SYSTEME_COMBUSTION_GAZ,
            79 => self::AUTRE_SYSTEME_COMBUSTION_FIOUL,
            80 => self::AUTRE_SYSTEME_COMBUSTION_BOIS,
            81 => self::AUTRE_SYSTEME_COMBUSTION_AUTRES_ENERGIES_FOSSILES,
            82 => self::AUTRE_SYSTEME_THERMODYNAMIQUE_ELECTRIQUE,
            83 => self::AUTRE_SYSTEME_THERMODYNAMIQUE_GAZ,
            84 => self::SYSTEME_COLLECTIF_DEFAUT,
            85, 86, 87, 88, 89, 90, 91 => self::CHAUDIERE_CHARBON,
            92, 93, 94 => self::CHAUDIERE_GPL_PROPANE_BUTANE_CLASSIQUE,
            95, 96, 97 => self::CHAUDIERE_GPL_PROPANE_BUTANE_STANDARD,
            98, 99, 100 => self::CHAUDIERE_GPL_PROPANE_BUTANE_BASSE_TEMPERATURE,
            101, 102, 103, 104 => self::CHAUDIERE_GPL_PROPANE_BUTANE_CONDENSATION,
            105, 106, 107 => self::ACCUMULATEUR_GPL_PROPANE_BUTANE_CLASSIQUE,
            108, 109 => self::ACCUMULATEUR_GPL_PROPANE_BUTANE_CONDENSATION,
            110, 111, 112, 113, 114 => self::CHAUFFE_EAU_GPL_PROPANE_BUTANE_PRODUCTION_INSTANTANEE,
            115, 116 => self::POÊLE_BOIS_BOUILLEUR_GRANULES,
            117 => self::CHAUFFE_EAU_ELECTRIQUE_INSTANTANE,
            118 => self::CHAUDIERE_ELECTRIQUE,
            119 => self::RESEAU_CHALEUR_NON_REPERTORIE_OU_INCONNU,
            120, 121 => self::POMPE_CHALEUR_HYBRIDE_PARTIE_CHAUDIERE_GAZ_CONDENSATION,
            122, 123 => self::POMPE_CHALEUR_HYBRIDE_PARTIE_CHAUDIERE_FIOUL_CONDENSATION,
            124, 125 => self::POMPE_CHALEUR_HYBRIDE_PARTIE_CHAUDIERE_BOIS_GRANULES,
            126, 127, 128 => self::POMPE_CHALEUR_HYBRIDE_PARTIE_CHAUDIERE_BOIS_BUCHE,
            129, 130, 131 => self::POMPE_CHALEUR_HYBRIDE_PARTIE_CHAUDIERE_BOIS_PLAQUETTE,
            132, 133 => self::POMPE_CHALEUR_HYBRIDE_PARTIE_CHAUDIERE_GPL_PROPANE_BUTANE_CONDENSATION,
            134 => self::CHAUDIERE_CHARBON_MULTI_BATIMENT,
        };
    }

    public function id(): int
    {
        return $this->value;
    }

    public function lib(): string
    {
        return match ($this) {
            self::CET_SUR_AIR_AMBIANT => 'CET sur air ambiant',
            self::CET_SUR_AIR_EXTERIEUR => 'CET sur air extérieur',
            self::CET_SUR_AIR_EXTRAIT => 'CET sur air extrait',
            self::PAC_DOUBLE_SERVICE => 'PAC double service',
            self::POÊLE_BOIS_BOUILLEUR_BUCHE => 'Poêle à bois bouilleur bûche',
            self::CHAUDIERE_BOIS_BUCHE => 'Chaudière bois bûche',
            self::CHAUDIERE_BOIS_PLAQUETTE => 'Chaudière bois plaquette',
            self::CHAUDIERE_BOIS_GRANULES => 'Chaudière bois granulés',
            self::CHAUDIERE_FIOUL_CLASSIQUE => 'Chaudière fioul classique',
            self::CHAUDIERE_FIOUL_STANDARD => 'Chaudière fioul standard',
            self::CHAUDIERE_FIOUL_BASSE_TEMPERATURE => 'Chaudière fioul basse température',
            self::CHAUDIERE_FIOUL_CONDENSATION => 'Chaudière fioul à condensation',
            self::CHAUDIERE_GAZ_CLASSIQUE => 'Chaudière gaz classique',
            self::CHAUDIERE_GAZ_STANDARD => 'Chaudière gaz standard',
            self::CHAUDIERE_GAZ_BASSE_TEMPERATURE => 'Chaudière gaz basse température',
            self::CHAUDIERE_GAZ_CONDENSATION => 'Chaudière gaz à condensation',
            self::ACCUMULATEUR_GAZ_CLASSIQUE => 'Accumulateur gaz classique',
            self::ACCUMULATEUR_GAZ_CONDENSATION => 'Accumulateur gaz à condensation',
            self::CHAUFFE_EAU_GAZ_PRODUCTION_INSTANTANEE => 'Chauffe-eau gaz à production instantanée',
            self::BALLON_ELECTRIQUE_ACCUMULATION_HORIZONTAL => 'Ballon électrique à accumulation horizontal',
            self::BALLON_ELECTRIQUE_ACCUMULATION_VERTICAL_AUTRES_OU_INCONNUE => 'Ballon électrique à accumulation vertical Autres ou inconnue',
            self::BALLON_ELECTRIQUE_ACCUMULATION_VERTICAL_CATEGORIE_B_OU_2_ETOILES => 'Ballon électrique à accumulation vertical Catégorie B ou 2 étoiles',
            self::BALLON_ELECTRIQUE_ACCUMULATION_VERTICAL_CATEGORIE_C_OU_3_ETOILES => 'Ballon électrique à accumulation vertical Catégorie C ou 3 étoiles',
            self::RESEAU_CHALEUR_NON_ISOLE => 'Réseau de chaleur non isolé',
            self::RESEAU_CHALEUR_ISOLE => 'Réseau de chaleur isolé',
            self::CHAUDIERE_BOIS_MULTI_BATIMENT => 'Chaudière(s) bois multi bâtiment',
            self::CHAUDIERE_FIOUL_MULTI_BATIMENT => 'Chaudière(s) fioul multi bâtiment',
            self::CHAUDIERE_GAZ_MULTI_BATIMENT => 'Chaudière(s) gaz multi bâtiment',
            self::POMPE_CHALEUR_MULTI_BATIMENT => 'Pompe(s) à chaleur multi bâtiment',
            self::AUTRE_SYSTEME_COMBUSTION_GAZ => 'Autre système à combustion gaz',
            self::AUTRE_SYSTEME_COMBUSTION_FIOUL => 'Autre système à combustion fioul',
            self::AUTRE_SYSTEME_COMBUSTION_BOIS => 'Autre système à combustion bois',
            self::AUTRE_SYSTEME_COMBUSTION_AUTRES_ENERGIES_FOSSILES => 'Autre système à combustion autres energies fossiles (charbon,pétrole etc…)',
            self::AUTRE_SYSTEME_THERMODYNAMIQUE_ELECTRIQUE => 'Autre système thermodynamique électrique',
            self::AUTRE_SYSTEME_THERMODYNAMIQUE_GAZ => 'Autre système thermodynamique gaz',
            self::SYSTEME_COLLECTIF_DEFAUT => 'Système collectif par défaut en abscence d\'information : chaudière fioul pénalisante',
            self::CHAUDIERE_CHARBON => 'Chaudière charbon',
            self::CHAUDIERE_GPL_PROPANE_BUTANE_CLASSIQUE => 'Chaudière gpl/propane/butane classique',
            self::CHAUDIERE_GPL_PROPANE_BUTANE_STANDARD => 'Chaudière gpl/propane/butane standard',
            self::CHAUDIERE_GPL_PROPANE_BUTANE_BASSE_TEMPERATURE => 'Chaudière gpl/propane/butane basse température',
            self::CHAUDIERE_GPL_PROPANE_BUTANE_CONDENSATION => 'Chaudière gpl/propane/butane à condensation',
            self::ACCUMULATEUR_GPL_PROPANE_BUTANE_CLASSIQUE => 'Accumulateur gpl/propane/butane classique',
            self::ACCUMULATEUR_GPL_PROPANE_BUTANE_CONDENSATION => 'Accumulateur gpl/propane/butane à condensation',
            self::CHAUFFE_EAU_GPL_PROPANE_BUTANE_PRODUCTION_INSTANTANEE => 'Chauffe-eau gpl/propane/butane à production instantanée',
            self::POÊLE_BOIS_BOUILLEUR_GRANULES => 'Poêle à bois bouilleur granulés',
            self::CHAUFFE_EAU_ELECTRIQUE_INSTANTANE => 'Chauffe-eau électrique instantané',
            self::CHAUDIERE_ELECTRIQUE => 'Chaudière électrique',
            self::RESEAU_CHALEUR_NON_REPERTORIE_OU_INCONNU => 'Réseau de chaleur non répertorié ou inconnu',
            self::POMPE_CHALEUR_HYBRIDE_PARTIE_CHAUDIERE_GAZ_CONDENSATION => 'Pompe à chaleur hybride : partie chaudière Chaudière gaz à condensation',
            self::POMPE_CHALEUR_HYBRIDE_PARTIE_CHAUDIERE_FIOUL_CONDENSATION => 'Pompe à chaleur hybride : partie chaudière Chaudière fioul à condensation',
            self::POMPE_CHALEUR_HYBRIDE_PARTIE_CHAUDIERE_BOIS_GRANULES => 'Pompe à chaleur hybride : partie chaudière Chaudière bois granulés',
            self::POMPE_CHALEUR_HYBRIDE_PARTIE_CHAUDIERE_BOIS_BUCHE => 'Pompe à chaleur hybride : partie chaudière Chaudière bois bûche',
            self::POMPE_CHALEUR_HYBRIDE_PARTIE_CHAUDIERE_BOIS_PLAQUETTE => 'Pompe à chaleur hybride : partie chaudière Chaudière bois plaquette',
            self::POMPE_CHALEUR_HYBRIDE_PARTIE_CHAUDIERE_GPL_PROPANE_BUTANE_CONDENSATION => 'Pompe à chaleur hybride : partie chaudière Chaudière gpl/propane/butane à condensation',
            self::CHAUDIERE_CHARBON_MULTI_BATIMENT => 'Chaudière(s) charbon multi bâtiment',
        };
    }

    public function chauffe_eau_thermodynamique(): bool
    {
        return \in_array($this, [
            self::CET_SUR_AIR_AMBIANT,
            self::CET_SUR_AIR_EXTERIEUR,
            self::CET_SUR_AIR_EXTRAIT,
            self::PAC_DOUBLE_SERVICE,
            self::AUTRE_SYSTEME_THERMODYNAMIQUE_ELECTRIQUE,
            self::AUTRE_SYSTEME_THERMODYNAMIQUE_GAZ,
        ]);
    }

    public function ballon_electrique(): bool
    {
        return \in_array($this, [
            self::BALLON_ELECTRIQUE_ACCUMULATION_HORIZONTAL,
            self::BALLON_ELECTRIQUE_ACCUMULATION_VERTICAL_AUTRES_OU_INCONNUE,
            self::BALLON_ELECTRIQUE_ACCUMULATION_VERTICAL_CATEGORIE_B_OU_2_ETOILES,
            self::BALLON_ELECTRIQUE_ACCUMULATION_VERTICAL_CATEGORIE_C_OU_3_ETOILES,
        ]);
    }

    public function chauffe_eau_electrique(): bool
    {
        return \in_array($this, [
            self::BALLON_ELECTRIQUE_ACCUMULATION_HORIZONTAL,
            self::BALLON_ELECTRIQUE_ACCUMULATION_VERTICAL_AUTRES_OU_INCONNUE,
            self::BALLON_ELECTRIQUE_ACCUMULATION_VERTICAL_CATEGORIE_B_OU_2_ETOILES,
            self::BALLON_ELECTRIQUE_ACCUMULATION_VERTICAL_CATEGORIE_C_OU_3_ETOILES,
            self::CHAUFFE_EAU_ELECTRIQUE_INSTANTANE
        ]);
    }

    public function chaudiere_electrique(): bool
    {
        return \in_array($this, [self::CHAUDIERE_ELECTRIQUE]);
    }

    public function chauffe_eau_gaz_instantanne(): bool
    {
        return \in_array($this, [
            self::CHAUFFE_EAU_GAZ_PRODUCTION_INSTANTANEE,
            self::CHAUFFE_EAU_GPL_PROPANE_BUTANE_PRODUCTION_INSTANTANEE,
        ]);
    }

    public function accumulateur_gaz(): bool
    {
        return \in_array($this, [
            self::ACCUMULATEUR_GAZ_CLASSIQUE,
            self::ACCUMULATEUR_GAZ_CONDENSATION,
        ]);
    }

    public function reseau_chaleur(): bool
    {
        return \in_array($this, [
            self::RESEAU_CHALEUR_NON_ISOLE,
            self::RESEAU_CHALEUR_ISOLE,
            self::RESEAU_CHALEUR_NON_REPERTORIE_OU_INCONNU,
        ]);
    }

    public function multi_batiment(): bool
    {
        return \in_array($this, [
            self::CHAUDIERE_BOIS_MULTI_BATIMENT,
            self::CHAUDIERE_FIOUL_MULTI_BATIMENT,
            self::CHAUDIERE_GAZ_MULTI_BATIMENT,
            self::POMPE_CHALEUR_MULTI_BATIMENT,
            self::CHAUDIERE_CHARBON_MULTI_BATIMENT,
        ]);
    }

    public function generateur_combustion(): bool
    {
        return \in_array($this, [
            self::CHAUDIERE_BOIS_BUCHE,
            self::CHAUDIERE_BOIS_PLAQUETTE,
            self::CHAUDIERE_BOIS_GRANULES,
            self::CHAUDIERE_FIOUL_CLASSIQUE,
            self::CHAUDIERE_FIOUL_STANDARD,
            self::CHAUDIERE_FIOUL_BASSE_TEMPERATURE,
            self::CHAUDIERE_FIOUL_CONDENSATION,
            self::CHAUDIERE_GAZ_CLASSIQUE,
            self::CHAUDIERE_GAZ_STANDARD,
            self::CHAUDIERE_GAZ_BASSE_TEMPERATURE,
            self::CHAUDIERE_GAZ_CONDENSATION,
            self::ACCUMULATEUR_GAZ_CLASSIQUE,
            self::ACCUMULATEUR_GAZ_CONDENSATION,
            self::CHAUFFE_EAU_GAZ_PRODUCTION_INSTANTANEE,
            self::CHAUDIERE_BOIS_MULTI_BATIMENT,
            self::CHAUDIERE_FIOUL_MULTI_BATIMENT,
            self::CHAUDIERE_GAZ_MULTI_BATIMENT,
            self::AUTRE_SYSTEME_COMBUSTION_BOIS,
            self::AUTRE_SYSTEME_COMBUSTION_FIOUL,
            self::AUTRE_SYSTEME_COMBUSTION_GAZ,
            self::AUTRE_SYSTEME_COMBUSTION_AUTRES_ENERGIES_FOSSILES,
            self::SYSTEME_COLLECTIF_DEFAUT,
            self::CHAUDIERE_CHARBON,
            self::CHAUDIERE_GPL_PROPANE_BUTANE_CLASSIQUE,
            self::CHAUDIERE_GPL_PROPANE_BUTANE_STANDARD,
            self::CHAUDIERE_GPL_PROPANE_BUTANE_BASSE_TEMPERATURE,
            self::CHAUDIERE_GPL_PROPANE_BUTANE_CONDENSATION,
            self::ACCUMULATEUR_GPL_PROPANE_BUTANE_CLASSIQUE,
            self::ACCUMULATEUR_GPL_PROPANE_BUTANE_CONDENSATION,
            self::CHAUFFE_EAU_GPL_PROPANE_BUTANE_PRODUCTION_INSTANTANEE,
            self::POÊLE_BOIS_BOUILLEUR_BUCHE,
            self::POÊLE_BOIS_BOUILLEUR_GRANULES,
            self::POMPE_CHALEUR_HYBRIDE_PARTIE_CHAUDIERE_BOIS_BUCHE,
            self::POMPE_CHALEUR_HYBRIDE_PARTIE_CHAUDIERE_BOIS_GRANULES,
            self::POMPE_CHALEUR_HYBRIDE_PARTIE_CHAUDIERE_BOIS_PLAQUETTE,
            self::POMPE_CHALEUR_HYBRIDE_PARTIE_CHAUDIERE_FIOUL_CONDENSATION,
            self::POMPE_CHALEUR_HYBRIDE_PARTIE_CHAUDIERE_GAZ_CONDENSATION,
            self::POMPE_CHALEUR_HYBRIDE_PARTIE_CHAUDIERE_GPL_PROPANE_BUTANE_CONDENSATION,
            self::CHAUDIERE_CHARBON_MULTI_BATIMENT,
        ]);
    }
}
