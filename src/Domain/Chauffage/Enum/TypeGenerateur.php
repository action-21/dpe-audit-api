<?php

namespace App\Domain\Chauffage\Enum;

enum TypeGenerateur: int
{
    case PAC_AIR_AIR = 1;
    case PAC_AIR_EAU = 2;
    case PAC_EAU_EAU = 3;
    case PAC_EAU_GLYCOLEE_EAU = 4;
    case PAC_GEOTHERMIQUE = 5;
    case CUISINIERE = 6;
    case FOYER_FERME = 7;
    case INSERT = 8;
    case POELE_BUCHE = 9;
    case POELE_GRANULES = 10;
    case POELE_GRANULES_FLAMME_VERTE = 11;
    case POELE_FIOUL_OU_GPL_OU_CHARBON = 12;
    case POELE_BOIS_BOUILLEUR_BUCHE = 13;
    case GENERATEUR_AIR_CHAUD_COMBUSTION = 14;
    case GENERATEUR_AIR_CHAUD_COMBUSTION_STANDARD = 15;
    case GENERATEUR_AIR_CHAUD_COMBUSTION_CONDENSATION = 16;
    case RADIATEUR_GAZ_INDEPENDANT_OU_AUTONOME = 17;
    case CHAUDIERE_BOIS_BUCHE = 18;
    case CHAUDIERE_BOIS_PLAQUETTE = 19;
    case CHAUDIERE_BOIS_GRANULES = 20;
    case CHAUDIERE_FIOUL_CLASSIQUE = 21;
    case CHAUDIERE_FIOUL_STANDARD = 22;
    case CHAUDIERE_FIOUL_BASSE_TEMPERATURE = 23;
    case CHAUDIERE_FIOUL_CONDENSATION = 24;
    case CHAUDIERE_GAZ_CLASSIQUE = 25;
    case CHAUDIERE_GAZ_STANDARD = 26;
    case CHAUDIERE_GAZ_BASSE_TEMPERATURE = 27;
    case CHAUDIERE_GAZ_CONDENSATION = 28;
    case CONVECTEUR_ELECTRIQUE_NFC = 29;
    case PANNEAU_RAYONNANT_ELECTRIQUE_NFC = 30;
    case RADIATEUR_ELECTRIQUE_NFC = 31;
    case AUTRES_EMETTEURS_EFFET_JOULE = 32;
    case PLANCHER_OU_PLAFOND_RAYONNANT_ELECTRIQUE_AVEC_REGULATION_TERMINALE = 33;
    case PLANCHER_OU_PLAFOND_RAYONNANT_ELECTRIQUE_SANS_REGULATION_TERMINALE = 34;
    case RADIATEUR_ELECTRIQUE_ACCUMULATION = 35;
    case CONVECTEUR_BI_JONCTION = 36;
    case CHAUDIERE_ELECTRIQUE = 37;
    case RESEAU_CHALEUR_NON_ISOLE = 38;
    case RESEAU_CHALEUR_ISOLE = 39;
    case CHAUDIERE_BOIS_MULTI_BATIMENT = 40;
    case CHAUDIERE_FIOUL_MULTI_BATIMENT = 41;
    case CHAUDIERE_GAZ_MULTI_BATIMENT = 42;
    case PAC_MULTI_BATIMENT = 43;
    /** @deprecated */
    case AUTRE_SYSTEME_COMBUSTION_GAZ = 44;
    /** @deprecated */
    case AUTRE_SYSTEME_COMBUSTION_FIOUL = 45;
    /** @deprecated */
    case AUTRE_SYSTEME_COMBUSTION_BOIS = 46;
    /** @deprecated */
    case AUTRE_SYSTEME_COMBUSTION_AUTRES_ENERGIES_FOSSILES = 47;
    /** @deprecated */
    case AUTRE_SYSTEME_THERMODYNAMIQUE_ELECTRIQUE = 48;
    /** @deprecated */
    case AUTRE_SYSTEME_THERMODYNAMIQUE_GAZ = 49;
    case SYSTEME_COLLECTIF_PAR_DEFAUT = 50;
    case CHAUDIERE_CHARBON = 51;
    case CHAUDIERE_GPL_PROPANE_BUTANE_CLASSIQUE = 52;
    case CHAUDIERE_GPL_PROPANE_BUTANE_STANDARD = 53;
    case CHAUDIERE_GPL_PROPANE_BUTANE_BASSE_TEMPERATURE = 54;
    case CHAUDIERE_GPL_PROPANE_BUTANE_CONDENSATION = 55;
    case POELE_BOIS_BOUILLEUR_GRANULES = 56;
    case RESEAU_CHALEUR_NON_REPERTORIE_OU_INCONNU = 57;
    /** @deprecated */
    case PAC_HYBRIDE_PARTIE_PAC = 58;
    /** @deprecated */
    case PAC_HYBRIDE_PARTIE_CHAUDIERE = 59;
    case PAC_HYBRIDE_PARTIE_PAC_AIR_EAU = 60;
    case PAC_HYBRIDE_PARTIE_CHAUDIERE_GAZ_CONDENSATION = 61;
    case PAC_HYBRIDE_PARTIE_CHAUDIERE_FIOUL_CONDENSATION = 62;
    /** @deprecated */
    case PAC_HYBRIDE_PARTIE_CHAUDIERE_BOIS_GRANULES = 63;
    /** @deprecated */
    case PAC_HYBRIDE_PARTIE_CHAUDIERE_BOIS_BUCHE = 64;
    /** @deprecated */
    case PAC_HYBRIDE_PARTIE_CHAUDIERE_BOIS_PLAQUETTE = 65;
    case PAC_HYBRIDE_PARTIE_CHAUDIERE_GPL_PROPANE_BUTANE_CONDENSATION = 66;
    case PAC_HYBRIDE_PARTIE_PAC_EAU_EAU = 67;
    case PAC_HYBRIDE_PARTIE_PAC_EAU_GLYCOLEE_EAU = 68;
    /** @deprecated */
    case PAC_HYBRIDE_PARTIE_PAC_GEOTHERMIQUE = 69;
    case CHAUDIERE_CHARBON_MULTI_BATIMENT = 70;

    public static function from_enum_type_generateur_id(int $id): self
    {
        return match ($id) {
            1, 2, 3 => self::PAC_AIR_AIR,
            4, 5, 6, 7 => self::PAC_AIR_EAU,
            8, 9, 10, 11 => self::PAC_EAU_EAU,
            12, 13, 14, 15 => self::PAC_EAU_GLYCOLEE_EAU,
            16, 17, 18, 19 => self::PAC_GEOTHERMIQUE,
            20, 24, 28, 32, 36, 40 => self::CUISINIERE,
            21, 25, 29, 33, 37, 41 => self::FOYER_FERME,
            23, 27, 31, 35, 39, 43 => self::INSERT,
            22, 26, 30, 34, 38, 42 => self::POELE_BUCHE,
            44 => self::POELE_GRANULES,
            45, 46 => self::POELE_GRANULES_FLAMME_VERTE,
            47 => self::POELE_FIOUL_OU_GPL_OU_CHARBON,
            48, 49 => self::POELE_BOIS_BOUILLEUR_BUCHE,
            50 => self::GENERATEUR_AIR_CHAUD_COMBUSTION,
            51 => self::GENERATEUR_AIR_CHAUD_COMBUSTION_STANDARD,
            52 => self::GENERATEUR_AIR_CHAUD_COMBUSTION_CONDENSATION,
            53, 54 => self::RADIATEUR_GAZ_INDEPENDANT_OU_AUTONOME,
            55, 56, 57, 58, 59, 60, 61 => self::CHAUDIERE_BOIS_BUCHE,
            62, 63, 64, 65, 66, 67, 68 => self::CHAUDIERE_BOIS_PLAQUETTE,
            69, 70, 71, 72, 73, 74 => self::CHAUDIERE_BOIS_GRANULES,
            75, 76, 77, 78 => self::CHAUDIERE_FIOUL_CLASSIQUE,
            79, 80 => self::CHAUDIERE_FIOUL_STANDARD,
            81, 82 => self::CHAUDIERE_FIOUL_BASSE_TEMPERATURE,
            83, 84 => self::CHAUDIERE_FIOUL_CONDENSATION,
            85, 86, 87 => self::CHAUDIERE_GAZ_CLASSIQUE,
            88, 89, 90 => self::CHAUDIERE_GAZ_STANDARD,
            91, 92, 93 => self::CHAUDIERE_GAZ_BASSE_TEMPERATURE,
            94, 95, 96, 97 => self::CHAUDIERE_GAZ_CONDENSATION,
            98 => self::CONVECTEUR_ELECTRIQUE_NFC,
            99 => self::PANNEAU_RAYONNANT_ELECTRIQUE_NFC,
            100 => self::RADIATEUR_ELECTRIQUE_NFC,
            101 => self::AUTRES_EMETTEURS_EFFET_JOULE,
            102 => self::PLANCHER_OU_PLAFOND_RAYONNANT_ELECTRIQUE_AVEC_REGULATION_TERMINALE,
            103 => self::PLANCHER_OU_PLAFOND_RAYONNANT_ELECTRIQUE_SANS_REGULATION_TERMINALE,
            104 => self::RADIATEUR_ELECTRIQUE_ACCUMULATION,
            105 => self::CONVECTEUR_BI_JONCTION,
            106 => self::CHAUDIERE_ELECTRIQUE,
            107 => self::RESEAU_CHALEUR_NON_ISOLE,
            108 => self::RESEAU_CHALEUR_ISOLE,
            109 => self::CHAUDIERE_BOIS_MULTI_BATIMENT,
            110 => self::CHAUDIERE_FIOUL_MULTI_BATIMENT,
            111 => self::CHAUDIERE_GAZ_MULTI_BATIMENT,
            112 => self::PAC_MULTI_BATIMENT,
            113 => self::AUTRE_SYSTEME_COMBUSTION_GAZ,
            114 => self::AUTRE_SYSTEME_COMBUSTION_FIOUL,
            115 => self::AUTRE_SYSTEME_COMBUSTION_BOIS,
            116 => self::AUTRE_SYSTEME_COMBUSTION_AUTRES_ENERGIES_FOSSILES,
            117 => self::AUTRE_SYSTEME_THERMODYNAMIQUE_ELECTRIQUE,
            118 => self::AUTRE_SYSTEME_THERMODYNAMIQUE_GAZ,
            119 => self::SYSTEME_COLLECTIF_PAR_DEFAUT,
            120, 121, 122, 123, 124, 125, 126 => self::CHAUDIERE_CHARBON,
            127, 128, 129 => self::CHAUDIERE_GPL_PROPANE_BUTANE_CLASSIQUE,
            130, 131, 132 => self::CHAUDIERE_GPL_PROPANE_BUTANE_STANDARD,
            133, 134, 135 => self::CHAUDIERE_GPL_PROPANE_BUTANE_BASSE_TEMPERATURE,
            136, 137, 138, 139 => self::CHAUDIERE_GPL_PROPANE_BUTANE_CONDENSATION,
            140, 141 => self::POELE_BOIS_BOUILLEUR_GRANULES,
            142 => self::RESEAU_CHALEUR_NON_REPERTORIE_OU_INCONNU,
            143 => self::PAC_HYBRIDE_PARTIE_PAC,
            144 => self::PAC_HYBRIDE_PARTIE_CHAUDIERE,
            145, 146, 147 => self::PAC_HYBRIDE_PARTIE_PAC_AIR_EAU,
            148, 149 => self::PAC_HYBRIDE_PARTIE_CHAUDIERE_GAZ_CONDENSATION,
            150, 151 => self::PAC_HYBRIDE_PARTIE_CHAUDIERE_FIOUL_CONDENSATION,
            152, 153 => self::PAC_HYBRIDE_PARTIE_CHAUDIERE_BOIS_GRANULES,
            154, 155, 156 => self::PAC_HYBRIDE_PARTIE_CHAUDIERE_BOIS_BUCHE,
            157, 158, 159 => self::PAC_HYBRIDE_PARTIE_CHAUDIERE_BOIS_PLAQUETTE,
            160, 161 => self::PAC_HYBRIDE_PARTIE_CHAUDIERE_GPL_PROPANE_BUTANE_CONDENSATION,
            162, 163, 164 => self::PAC_HYBRIDE_PARTIE_PAC_EAU_EAU,
            165, 166, 167 => self::PAC_HYBRIDE_PARTIE_PAC_EAU_GLYCOLEE_EAU,
            168, 169, 170 => self::PAC_HYBRIDE_PARTIE_PAC_GEOTHERMIQUE,
            171 => self::CHAUDIERE_CHARBON_MULTI_BATIMENT,
        };
    }
    public function id(): int
    {
        return $this->value;
    }

    public function lib(): string
    {
        return match ($this) {
            self::PAC_AIR_AIR => 'PAC air/air',
            self::PAC_AIR_EAU => 'PAC air/eau',
            self::PAC_EAU_EAU => 'PAC eau/eau',
            self::PAC_EAU_GLYCOLEE_EAU => 'PAC eau glycolée/eau',
            self::PAC_GEOTHERMIQUE => 'PAC géothermique',
            self::CUISINIERE => 'Cuisinière',
            self::FOYER_FERME => 'Foyer fermé',
            self::INSERT => 'Insert',
            self::POELE_BUCHE => 'Poêle bûche',
            self::POELE_GRANULES => 'Poêle à granulés',
            self::POELE_GRANULES_FLAMME_VERTE => 'Poêle à granulés flamme verte',
            self::POELE_FIOUL_OU_GPL_OU_CHARBON => 'Poêle fioul ou GPL ou charbon',
            self::POELE_BOIS_BOUILLEUR_BUCHE => 'Poêle à bois bouilleur bûche',
            self::GENERATEUR_AIR_CHAUD_COMBUSTION => 'Générateur à air chaud à combustion',
            self::GENERATEUR_AIR_CHAUD_COMBUSTION_STANDARD => 'Générateur à air chaud à combustion standard',
            self::GENERATEUR_AIR_CHAUD_COMBUSTION_CONDENSATION => 'Générateur à air chaud à combustion à condensation',
            self::RADIATEUR_GAZ_INDEPENDANT_OU_AUTONOME => 'Radiateur à gaz indépendant ou autonome',
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
            self::CONVECTEUR_ELECTRIQUE_NFC => 'Convecteur électrique NFC, NF** et NF***',
            self::PANNEAU_RAYONNANT_ELECTRIQUE_NFC => 'Panneau rayonnant électrique NFC, NF** et NF***',
            self::RADIATEUR_ELECTRIQUE_NFC => 'Radiateur électrique NFC, NF** et NF***',
            self::AUTRES_EMETTEURS_EFFET_JOULE => 'Autres émetteurs à effet joule',
            self::PLANCHER_OU_PLAFOND_RAYONNANT_ELECTRIQUE_AVEC_REGULATION_TERMINALE => 'Plancher ou plafond rayonnant électrique avec régulation terminale',
            self::PLANCHER_OU_PLAFOND_RAYONNANT_ELECTRIQUE_SANS_REGULATION_TERMINALE => 'Plancher ou plafond rayonnant électrique sans régulation terminale',
            self::RADIATEUR_ELECTRIQUE_ACCUMULATION => 'Radiateur électrique à accumulation',
            self::CONVECTEUR_BI_JONCTION => 'Convecteur bi-jonction',
            self::CHAUDIERE_ELECTRIQUE => 'Chaudière électrique',
            self::RESEAU_CHALEUR_NON_ISOLE => 'Réseau de chaleur non isolé',
            self::RESEAU_CHALEUR_ISOLE => 'Réseau de chaleur isolé',
            self::CHAUDIERE_BOIS_MULTI_BATIMENT => 'Chaudière(s) bois multi bâtiment modélisée comme un réseau de chaleur',
            self::CHAUDIERE_FIOUL_MULTI_BATIMENT => 'Chaudière(s) fioul multi bâtiment modélisée comme un réseau de chaleur',
            self::CHAUDIERE_GAZ_MULTI_BATIMENT => 'Chaudière(s) gaz multi bâtiment modélisée comme un réseau de chaleur',
            self::PAC_MULTI_BATIMENT => 'Pompe(s) à chaleur multi bâtiment modélisée comme un réseau de chaleur',
            self::AUTRE_SYSTEME_COMBUSTION_GAZ => 'Autre système à combustion gaz',
            self::AUTRE_SYSTEME_COMBUSTION_FIOUL => 'Autre système à combustion fioul',
            self::AUTRE_SYSTEME_COMBUSTION_BOIS => 'Autre système à combustion bois',
            self::AUTRE_SYSTEME_COMBUSTION_AUTRES_ENERGIES_FOSSILES => 'Autre système à combustion autres energies fossiles (charbon,pétrole etc…)',
            self::AUTRE_SYSTEME_THERMODYNAMIQUE_ELECTRIQUE => 'Autre système thermodynamique électrique',
            self::AUTRE_SYSTEME_THERMODYNAMIQUE_GAZ => 'Autre système thermodynamique gaz',
            self::SYSTEME_COLLECTIF_PAR_DEFAUT => 'Système collectif par défaut en abscence d\'information : chaudière fioul pénalisante',
            self::CHAUDIERE_CHARBON => 'Chaudière charbon',
            self::CHAUDIERE_GPL_PROPANE_BUTANE_CLASSIQUE => 'Chaudière gpl/propane/butane classique',
            self::CHAUDIERE_GPL_PROPANE_BUTANE_STANDARD => 'Chaudière gpl/propane/butane standard',
            self::CHAUDIERE_GPL_PROPANE_BUTANE_BASSE_TEMPERATURE => 'Chaudière gpl/propane/butane basse température',
            self::CHAUDIERE_GPL_PROPANE_BUTANE_CONDENSATION => 'Chaudière gpl/propane/butane à condensation',
            self::POELE_BOIS_BOUILLEUR_GRANULES => 'Poêle à bois bouilleur granulés',
            self::RESEAU_CHALEUR_NON_REPERTORIE_OU_INCONNU => 'Réseau de chaleur non répertorié ou inconnu',
            self::PAC_HYBRIDE_PARTIE_PAC => 'Pompe à chaleur hybride : partie pompe à chaleur (SUPPRIME)',
            self::PAC_HYBRIDE_PARTIE_CHAUDIERE => 'Pompe à chaleur hybride : partie chaudière (SUPPRIME)',
            self::PAC_HYBRIDE_PARTIE_PAC_AIR_EAU => 'Pompe à chaleur hybride : partie pompe à chaleur PAC air/eau',
            self::PAC_HYBRIDE_PARTIE_CHAUDIERE_GAZ_CONDENSATION => 'Pompe à chaleur hybride : partie chaudière Chaudière gaz à condensation',
            self::PAC_HYBRIDE_PARTIE_CHAUDIERE_FIOUL_CONDENSATION => 'Pompe à chaleur hybride : partie chaudière Chaudière fioul à condensation',
            self::PAC_HYBRIDE_PARTIE_CHAUDIERE_BOIS_GRANULES => 'Pompe à chaleur hybride : partie chaudière Chaudière bois granulés',
            self::PAC_HYBRIDE_PARTIE_CHAUDIERE_BOIS_BUCHE => 'Pompe à chaleur hybride : partie chaudière Chaudière bois bûche',
            self::PAC_HYBRIDE_PARTIE_CHAUDIERE_BOIS_PLAQUETTE => 'Pompe à chaleur hybride : partie chaudière Chaudière bois plaquette',
            self::PAC_HYBRIDE_PARTIE_CHAUDIERE_GPL_PROPANE_BUTANE_CONDENSATION => 'Pompe à chaleur hybride : partie chaudière Chaudière gpl/propane/butane à condensation',
            self::PAC_HYBRIDE_PARTIE_PAC_EAU_EAU => 'Pompe à chaleur hybride : partie pompe à chaleur PAC eau/eau',
            self::PAC_HYBRIDE_PARTIE_PAC_EAU_GLYCOLEE_EAU => 'Pompe à chaleur hybride : partie pompe à chaleur PAC eau glycolée/eau',
            self::PAC_HYBRIDE_PARTIE_PAC_GEOTHERMIQUE => 'Pompe à chaleur hybride : partie pompe à chaleur PAC géothermique',
            self::CHAUDIERE_CHARBON_MULTI_BATIMENT => 'Chaudière(s) charbon multi bâtiment modélisée comme un réseau de chaleur',
        };
    }

    public function chaudiere(): bool
    {
        return $this->autre_chaudiere()
            || $this->chaudiere_bois()
            || $this->chaudiere_charbon()
            || $this->chaudiere_electrique()
            || $this->chaudiere_fioul()
            || $this->chaudiere_gaz()
            || $this->chaudiere_multi_batiment()
            || $this->poele_bois_bouilleur();
    }

    public function autre_chaudiere(): bool
    {
        return \in_array($this, [
            self::AUTRE_SYSTEME_COMBUSTION_AUTRES_ENERGIES_FOSSILES,
            self::SYSTEME_COLLECTIF_PAR_DEFAUT,
            self::PAC_HYBRIDE_PARTIE_CHAUDIERE,
        ]);
    }

    public function chaudiere_bois(): bool
    {
        return \in_array($this, [
            self::CHAUDIERE_BOIS_BUCHE,
            self::CHAUDIERE_BOIS_PLAQUETTE,
            self::CHAUDIERE_BOIS_GRANULES,
            self::AUTRE_SYSTEME_COMBUSTION_BOIS,
            self::PAC_HYBRIDE_PARTIE_CHAUDIERE_BOIS_BUCHE,
            self::PAC_HYBRIDE_PARTIE_CHAUDIERE_BOIS_GRANULES,
            self::PAC_HYBRIDE_PARTIE_CHAUDIERE_BOIS_PLAQUETTE,
        ]);
    }

    public function chaudiere_charbon(): bool
    {
        return \in_array($this, [
            self::CHAUDIERE_CHARBON,
        ]);
    }

    public function chaudiere_electrique(): bool
    {
        return \in_array($this, [
            self::CHAUDIERE_ELECTRIQUE,
        ]);
    }

    public function chaudiere_fioul(): bool
    {
        return \in_array($this, [
            self::CHAUDIERE_FIOUL_CLASSIQUE,
            self::CHAUDIERE_FIOUL_STANDARD,
            self::CHAUDIERE_FIOUL_BASSE_TEMPERATURE,
            self::CHAUDIERE_FIOUL_CONDENSATION,
            self::AUTRE_SYSTEME_COMBUSTION_FIOUL,
            self::PAC_HYBRIDE_PARTIE_CHAUDIERE_FIOUL_CONDENSATION,
        ]);
    }

    public function chaudiere_gaz(): bool
    {
        return \in_array($this, [
            self::CHAUDIERE_GAZ_CLASSIQUE,
            self::CHAUDIERE_GAZ_STANDARD,
            self::CHAUDIERE_GAZ_BASSE_TEMPERATURE,
            self::CHAUDIERE_GAZ_CONDENSATION,
            self::AUTRE_SYSTEME_COMBUSTION_GAZ,
            self::PAC_HYBRIDE_PARTIE_CHAUDIERE_GAZ_CONDENSATION,
            self::CHAUDIERE_GPL_PROPANE_BUTANE_CLASSIQUE,
            self::CHAUDIERE_GPL_PROPANE_BUTANE_STANDARD,
            self::CHAUDIERE_GPL_PROPANE_BUTANE_BASSE_TEMPERATURE,
            self::CHAUDIERE_GPL_PROPANE_BUTANE_CONDENSATION,
            self::PAC_HYBRIDE_PARTIE_CHAUDIERE_GPL_PROPANE_BUTANE_CONDENSATION,
        ]);
    }

    public function chaudiere_multi_batiment(): bool
    {
        return \in_array($this, [
            self::CHAUDIERE_GAZ_MULTI_BATIMENT,
            self::CHAUDIERE_FIOUL_MULTI_BATIMENT,
            self::CHAUDIERE_BOIS_MULTI_BATIMENT,
            self::PAC_MULTI_BATIMENT,
            self::CHAUDIERE_CHARBON_MULTI_BATIMENT,
        ]);
    }

    public function chauffage_electrique(): bool
    {
        return \in_array($this, [
            self::CONVECTEUR_ELECTRIQUE_NFC,
            self::PANNEAU_RAYONNANT_ELECTRIQUE_NFC,
            self::RADIATEUR_ELECTRIQUE_NFC,
            self::AUTRES_EMETTEURS_EFFET_JOULE,
            self::PLANCHER_OU_PLAFOND_RAYONNANT_ELECTRIQUE_AVEC_REGULATION_TERMINALE,
            self::PLANCHER_OU_PLAFOND_RAYONNANT_ELECTRIQUE_SANS_REGULATION_TERMINALE,
            self::RADIATEUR_ELECTRIQUE_ACCUMULATION,
            self::CONVECTEUR_BI_JONCTION,
        ]);
    }

    public function generateur_air_chaud(): bool
    {
        return \in_array($this, [
            self::GENERATEUR_AIR_CHAUD_COMBUSTION,
            self::GENERATEUR_AIR_CHAUD_COMBUSTION_STANDARD,
            self::GENERATEUR_AIR_CHAUD_COMBUSTION_CONDENSATION,
        ]);
    }

    public function pac(): bool
    {
        return \in_array($this, [
            self::PAC_AIR_AIR,
            self::PAC_AIR_EAU,
            self::PAC_EAU_EAU,
            self::PAC_EAU_GLYCOLEE_EAU,
            self::PAC_GEOTHERMIQUE,
            self::PAC_HYBRIDE_PARTIE_PAC_AIR_EAU,
            self::PAC_HYBRIDE_PARTIE_PAC_EAU_EAU,
            self::PAC_HYBRIDE_PARTIE_PAC_EAU_GLYCOLEE_EAU,
            self::PAC_HYBRIDE_PARTIE_PAC_GEOTHERMIQUE,
            self::PAC_HYBRIDE_PARTIE_PAC,
            self::AUTRE_SYSTEME_THERMODYNAMIQUE_ELECTRIQUE,
            self::AUTRE_SYSTEME_THERMODYNAMIQUE_GAZ,
        ]);
    }

    public function poele_bois_bouilleur(): bool
    {
        return \in_array($this, [
            self::POELE_BOIS_BOUILLEUR_BUCHE,
            self::POELE_BOIS_BOUILLEUR_GRANULES,
        ]);
    }

    public function poele_insert(): bool
    {
        return \in_array($this, [
            self::CUISINIERE,
            self::FOYER_FERME,
            self::INSERT,
            self::POELE_BUCHE,
            self::POELE_GRANULES,
            self::POELE_GRANULES_FLAMME_VERTE,
            self::POELE_FIOUL_OU_GPL_OU_CHARBON,
        ]);
    }

    public function radiateur_gaz(): bool
    {
        return \in_array($this, [
            self::RADIATEUR_GAZ_INDEPENDANT_OU_AUTONOME,
        ]);
    }

    public function reseau_chaleur(): bool
    {
        return \in_array($this, [
            self::RESEAU_CHALEUR_ISOLE,
            self::RESEAU_CHALEUR_NON_ISOLE,
            self::RESEAU_CHALEUR_NON_REPERTORIE_OU_INCONNU,
        ]);
    }

    public function combustion_standard(): bool
    {
        return \in_array($this, [
            self::CHAUDIERE_FIOUL_CLASSIQUE,
            self::CHAUDIERE_FIOUL_STANDARD,
            self::CHAUDIERE_GAZ_CLASSIQUE,
            self::CHAUDIERE_GAZ_STANDARD,
            self::CHAUDIERE_GPL_PROPANE_BUTANE_CLASSIQUE,
            self::CHAUDIERE_GPL_PROPANE_BUTANE_STANDARD,
            self::AUTRE_SYSTEME_COMBUSTION_GAZ,
            self::AUTRE_SYSTEME_COMBUSTION_FIOUL,
            self::PAC_HYBRIDE_PARTIE_CHAUDIERE,
            self::AUTRE_SYSTEME_COMBUSTION_AUTRES_ENERGIES_FOSSILES,
            self::SYSTEME_COLLECTIF_PAR_DEFAUT,
        ]);
    }

    public function combustion_basse_temperature(): bool
    {
        return \in_array($this, [
            self::CHAUDIERE_FIOUL_BASSE_TEMPERATURE,
            self::CHAUDIERE_GAZ_BASSE_TEMPERATURE,
            self::CHAUDIERE_GPL_PROPANE_BUTANE_BASSE_TEMPERATURE,
        ]);
    }

    public function combustion_condensation(): bool
    {
        return \in_array($this, [
            self::CHAUDIERE_FIOUL_CONDENSATION,
            self::CHAUDIERE_GAZ_CONDENSATION,
            self::CHAUDIERE_GPL_PROPANE_BUTANE_CONDENSATION,
            self::PAC_HYBRIDE_PARTIE_CHAUDIERE_GAZ_CONDENSATION,
            self::PAC_HYBRIDE_PARTIE_CHAUDIERE_GPL_PROPANE_BUTANE_CONDENSATION,
            self::PAC_HYBRIDE_PARTIE_CHAUDIERE_FIOUL_CONDENSATION,
        ]);
    }

    public function effet_joule(): bool
    {
        return \in_array($this, [
            self::CONVECTEUR_ELECTRIQUE_NFC,
            self::PANNEAU_RAYONNANT_ELECTRIQUE_NFC,
            self::RADIATEUR_ELECTRIQUE_NFC,
            self::AUTRES_EMETTEURS_EFFET_JOULE,
            self::PLANCHER_OU_PLAFOND_RAYONNANT_ELECTRIQUE_AVEC_REGULATION_TERMINALE,
            self::PLANCHER_OU_PLAFOND_RAYONNANT_ELECTRIQUE_SANS_REGULATION_TERMINALE,
            self::RADIATEUR_ELECTRIQUE_ACCUMULATION,
            self::CONVECTEUR_BI_JONCTION,
        ]);
    }

    public function position_volume_chauffe(): ?bool
    {
        return match (true) {
            \in_array($this, [
                self::CUISINIERE,
                self::FOYER_FERME,
                self::INSERT,
                self::POELE_BUCHE,
                self::POELE_GRANULES,
                self::POELE_GRANULES_FLAMME_VERTE,
                self::POELE_FIOUL_OU_GPL_OU_CHARBON,
                self::RADIATEUR_GAZ_INDEPENDANT_OU_AUTONOME,
                self::CONVECTEUR_ELECTRIQUE_NFC,
                self::PANNEAU_RAYONNANT_ELECTRIQUE_NFC,
                self::RADIATEUR_ELECTRIQUE_NFC,
                self::AUTRES_EMETTEURS_EFFET_JOULE,
                self::PLANCHER_OU_PLAFOND_RAYONNANT_ELECTRIQUE_AVEC_REGULATION_TERMINALE,
                self::PLANCHER_OU_PLAFOND_RAYONNANT_ELECTRIQUE_SANS_REGULATION_TERMINALE,
                self::RADIATEUR_ELECTRIQUE_ACCUMULATION,
                self::CONVECTEUR_BI_JONCTION,
            ]) => true,
            \in_array($this, [
                self::RESEAU_CHALEUR_ISOLE,
                self::RESEAU_CHALEUR_NON_ISOLE,
                self::CHAUDIERE_BOIS_MULTI_BATIMENT,
                self::CHAUDIERE_FIOUL_MULTI_BATIMENT,
                self::CHAUDIERE_GAZ_MULTI_BATIMENT,
                self::PAC_MULTI_BATIMENT,
                self::SYSTEME_COLLECTIF_PAR_DEFAUT,
                self::RESEAU_CHALEUR_NON_REPERTORIE_OU_INCONNU,
                self::CHAUDIERE_CHARBON_MULTI_BATIMENT,
            ]) => false,
            default => null,
        };
    }

    public function combustion(): bool
    {
        return $this->combustion_condensation()
            || $this->combustion_basse_temperature()
            || $this->combustion_standard()
            || $this->chaudiere_bois()
            || $this->chaudiere_charbon()
            || $this->generateur_air_chaud()
            || $this->radiateur_gaz()
            || $this->poele_bois_bouilleur();
    }

    public function emission_multiple(): bool
    {
        if ($this->effet_joule() || $this->generateur_air_chaud() || $this->poele_insert() || $this->radiateur_gaz()) {
            return false;
        }
        return true;
    }

    public function rpn_sup_rpint(): ?bool
    {
        if ($this->combustion_condensation()) {
            return false;
        }
        if ($this->combustion_standard()) {
            return true;
        }
        return null;
    }
}
