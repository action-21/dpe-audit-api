<?php

namespace App\Domain\Chauffage\Enum;

use App\Domain\Common\Enum\Enum;

enum TypeGenerateur: string implements Enum
{
    case CHAUDIERE = 'CHAUDIERE';
    case CHAUDIERE_MULTI_BATIMENT = 'CHAUDIERE_MULTI_BATIMENT';
    case CONVECTEUR_BI_JONCTION = 'CONVECTEUR_BI_JONCTION';
    case CONVECTEUR_ELECTRIQUE = 'CONVECTEUR_ELECTRIQUE';
    case CUISINIERE = 'CUISINIERE';
    case FOYER_FERME = 'FOYER_FERME';
    case GENERATEUR_AIR_CHAUD = 'GENERATEUR_AIR_CHAUD';
    case INSERT = 'INSERT';
    case PAC_AIR_AIR = 'PAC_AIR_AIR';
    case PAC_AIR_EAU = 'PAC_AIR_EAU';
    case PAC_EAU_EAU = 'PAC_EAU_EAU';
    case PAC_EAU_GLYCOLEE_EAU = 'PAC_EAU_GLYCOLEE_EAU';
    case PAC_GEOTHERMIQUE = 'PAC_GEOTHERMIQUE';
    case PAC_HYBRIDE_AIR_EAU = 'PAC_HYBRIDE_AIR_EAU';
    case PAC_HYBRIDE_EAU_EAU = 'PAC_HYBRIDE_EAU_EAU';
    case PAC_HYBRIDE_EAU_GLYCOLEE_EAU = 'PAC_HYBRIDE_EAU_GLYCOLEE_EAU';
    case PAC_HYBRIDE_GEOTHERMIQUE = 'PAC_HYBRIDE_GEOTHERMIQUE';
    case PAC_MULTI_BATIMENT = 'PAC_MULTI_BATIMENT';
    case PANNEAU_RAYONNANT_ELECTRIQUE = 'PANNEAU_RAYONNANT_ELECTRIQUE';
    case PLAFOND_RAYONNANT_ELECTRIQUE = 'PLAFOND_RAYONNANT_ELECTRIQUE';
    case PLANCHER_RAYONNANT_ELECTRIQUE = 'PLANCHER_RAYONNANT_ELECTRIQUE';
    case POELE = 'POELE';
    case POELE_BOUILLEUR = 'POELE_BOUILLEUR';
    case RADIATEUR_ELECTRIQUE = 'RADIATEUR_ELECTRIQUE';
    case RADIATEUR_ELECTRIQUE_ACCUMULATION = 'RADIATEUR_ELECTRIQUE_ACCUMULATION';
    case RADIATEUR_GAZ = 'RADIATEUR_GAZ';
    case RESEAU_CHALEUR = 'RESEAU_CHALEUR';

    public static function from_type_generateur_ch_id(int $id): ?self
    {
        return match ($id) {
            55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 77, 78, 79, 80,
            81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 92, 93, 94, 95, 96, 97, 106, 119, 120, 121, 122, 123, 124, 125,
            126, 127, 128, 129, 130, 131, 132, 133, 134, 135, 136, 137, 138, 139 => self::CHAUDIERE,
            109, 110, 111, 171 => self::CHAUDIERE_MULTI_BATIMENT,
            105 => self::CONVECTEUR_BI_JONCTION,
            98, 99, 100, 101 => self::CONVECTEUR_ELECTRIQUE,
            20, 24, 28, 32, 36, 40 => self::CUISINIERE,
            21, 25, 29, 33, 37, 41 => self::FOYER_FERME,
            50, 51, 52 => self::GENERATEUR_AIR_CHAUD,
            23, 27, 31, 35, 39, 43 => self::INSERT,
            1, 2, 3 => self::PAC_AIR_AIR,
            4, 5, 6, 7 => self::PAC_AIR_EAU,
            8, 9, 10, 11 => self::PAC_EAU_EAU,
            12, 13, 14, 15 => self::PAC_EAU_GLYCOLEE_EAU,
            16, 17, 18, 19 => self::PAC_GEOTHERMIQUE,
            148, 149, 150, 151, 152, 153, 154, 155, 156, 157, 158, 159, 160, 161 => self::PAC_HYBRIDE_AIR_EAU,
            112 => self::PAC_MULTI_BATIMENT,
            102 => self::PANNEAU_RAYONNANT_ELECTRIQUE,
            103 => self::PLANCHER_RAYONNANT_ELECTRIQUE,
            22, 26, 30, 34, 38, 42, 44, 45, 46, 47 => self::POELE,
            48, 49, 140, 141 => self::POELE_BOUILLEUR,
            101, 104 => self::RADIATEUR_ELECTRIQUE,
            53, 54 => self::RADIATEUR_GAZ,
            107, 108, 142 => self::RESEAU_CHALEUR,
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
            self::PAC_AIR_AIR => 'Pompe à chaleur air/air',
            self::PAC_AIR_EAU => 'Pompe à chaleur air/eau',
            self::PAC_EAU_EAU => 'Pompe à chaleur eau/eau',
            self::PAC_EAU_GLYCOLEE_EAU => 'Pompe à chaleur eau glycolee/eau',
            self::PAC_GEOTHERMIQUE => 'Pompe à chaleur geothermique',
            self::PAC_HYBRIDE_AIR_EAU => 'Pompe à chaleur hybride air/eau',
            self::PAC_HYBRIDE_EAU_EAU => 'Pompe à chaleur hybride eau/eau',
            self::PAC_HYBRIDE_EAU_GLYCOLEE_EAU => 'Pompe à chaleur hybride eau glycolee/eau',
            self::PAC_HYBRIDE_GEOTHERMIQUE => 'Pompe à chaleur hybride geothermique',
            self::CHAUDIERE => 'Chaudiere',
            self::CHAUDIERE_MULTI_BATIMENT => 'Chaudiere multi bâtiment',
            self::PAC_MULTI_BATIMENT => 'Pompe(s) à chaleur multi bâtiment modelisee comme un reseau de chaleur',
            self::CONVECTEUR_ELECTRIQUE => 'Convecteur electrique',
            self::PANNEAU_RAYONNANT_ELECTRIQUE => 'Panneau rayonnant electrique',
            self::RADIATEUR_ELECTRIQUE => 'Radiateur electrique',
            self::PLANCHER_RAYONNANT_ELECTRIQUE => 'Plancher rayonnant electrique',
            self::PLAFOND_RAYONNANT_ELECTRIQUE => 'Plafond rayonnant electrique',
            self::RADIATEUR_ELECTRIQUE_ACCUMULATION => 'Radiateur electrique à accumulation',
            self::CONVECTEUR_BI_JONCTION => 'Convecteur bi-jonction',
            self::GENERATEUR_AIR_CHAUD => 'Generateur à air chaud',
            self::POELE_BOUILLEUR => 'Poêle bouilleur',
            self::CUISINIERE => 'Cuisiniere',
            self::FOYER_FERME => 'Foyer ferme',
            self::INSERT => 'Insert',
            self::POELE => 'Poêle',
            self::RADIATEUR_GAZ => 'Radiateur à gaz',
            self::RESEAU_CHALEUR => 'Reseau de chaleur',
        };
    }

    public function is_chauffage_electrique(): bool
    {
        return \in_array($this, [
            self::PANNEAU_RAYONNANT_ELECTRIQUE,
            self::PLAFOND_RAYONNANT_ELECTRIQUE,
            self::PLANCHER_RAYONNANT_ELECTRIQUE,
            self::RADIATEUR_ELECTRIQUE,
            self::RADIATEUR_ELECTRIQUE_ACCUMULATION,
        ]);
    }

    public function is_poele_insert(): bool
    {
        return \in_array($this, [
            self::CUISINIERE,
            self::FOYER_FERME,
            self::INSERT,
            self::POELE,
        ]);
    }

    public function is_chaudiere(): bool
    {
        return \in_array($this, [
            self::CHAUDIERE,
            self::CHAUDIERE_MULTI_BATIMENT,
            self::PAC_HYBRIDE_AIR_EAU,
            self::PAC_HYBRIDE_EAU_EAU,
            self::PAC_HYBRIDE_EAU_GLYCOLEE_EAU,
            self::PAC_HYBRIDE_GEOTHERMIQUE,
        ]);
    }

    public function is_pac(): bool
    {
        return \in_array($this, [
            self::PAC_AIR_AIR,
            self::PAC_AIR_EAU,
            self::PAC_EAU_EAU,
            self::PAC_EAU_GLYCOLEE_EAU,
            self::PAC_GEOTHERMIQUE,
            self::PAC_HYBRIDE_AIR_EAU,
            self::PAC_HYBRIDE_EAU_EAU,
            self::PAC_HYBRIDE_EAU_GLYCOLEE_EAU,
            self::PAC_HYBRIDE_GEOTHERMIQUE,
            self::PAC_MULTI_BATIMENT,
        ]);
    }

    public function is_pac_hybride(): bool
    {
        return \in_array($this, [
            self::PAC_HYBRIDE_AIR_EAU,
            self::PAC_HYBRIDE_EAU_EAU,
            self::PAC_HYBRIDE_EAU_GLYCOLEE_EAU,
            self::PAC_HYBRIDE_GEOTHERMIQUE,
        ]);
    }

    public function is_reseau_chaleur(): bool
    {
        return $this === self::RESEAU_CHALEUR;
    }

    public function is_usage_mixte(): bool
    {
        return \in_array($this, [
            self::CHAUDIERE,
            self::CHAUDIERE_MULTI_BATIMENT,
            self::PAC_AIR_EAU,
            self::PAC_EAU_EAU,
            self::PAC_EAU_GLYCOLEE_EAU,
            self::PAC_GEOTHERMIQUE,
            self::PAC_HYBRIDE_AIR_EAU,
            self::PAC_HYBRIDE_EAU_EAU,
            self::PAC_HYBRIDE_EAU_GLYCOLEE_EAU,
            self::PAC_HYBRIDE_GEOTHERMIQUE,
            self::PAC_MULTI_BATIMENT,
            self::POELE_BOUILLEUR,
            self::RESEAU_CHALEUR,
        ]);
    }

    public function is_chauffage_central(): bool
    {
        return \in_array($this, [
            self::CHAUDIERE,
            self::CHAUDIERE_MULTI_BATIMENT,
            self::GENERATEUR_AIR_CHAUD,
            self::PAC_AIR_AIR,
            self::PAC_AIR_EAU,
            self::PAC_EAU_EAU,
            self::PAC_EAU_GLYCOLEE_EAU,
            self::PAC_GEOTHERMIQUE,
            self::PAC_HYBRIDE_AIR_EAU,
            self::PAC_HYBRIDE_EAU_EAU,
            self::PAC_HYBRIDE_EAU_GLYCOLEE_EAU,
            self::PAC_HYBRIDE_GEOTHERMIQUE,
            self::PAC_MULTI_BATIMENT,
            self::POELE_BOUILLEUR,
            self::RESEAU_CHALEUR,
        ]);
    }

    public function is_chauffage_divise(): bool
    {
        return \in_array($this, [
            self::CONVECTEUR_BI_JONCTION,
            self::CONVECTEUR_ELECTRIQUE,
            self::GENERATEUR_AIR_CHAUD,
            self::PANNEAU_RAYONNANT_ELECTRIQUE,
            self::PLAFOND_RAYONNANT_ELECTRIQUE,
            self::PLANCHER_RAYONNANT_ELECTRIQUE,
            self::RADIATEUR_ELECTRIQUE,
            self::RADIATEUR_ELECTRIQUE_ACCUMULATION,
            self::PAC_AIR_AIR,
            self::CUISINIERE,
            self::FOYER_FERME,
            self::INSERT,
            self::POELE,
            self::RADIATEUR_GAZ,
        ]);
    }

    public function is_generateur_collectif(): ?bool
    {
        if ($this->is_chauffage_electrique() || $this->is_poele_insert()) {
            return false;
        }
        return match ($this) {
            self::RADIATEUR_GAZ => false,
            self::CHAUDIERE_MULTI_BATIMENT, self::PAC_MULTI_BATIMENT, self::RESEAU_CHALEUR => true,
            default => null,
        };
    }

    public function position_volume_chauffe(): ?bool
    {
        if ($this->is_chauffage_electrique()) {
            return true;
        }
        return match ($this) {
            self::CHAUDIERE_MULTI_BATIMENT, self::PAC_MULTI_BATIMENT, self::RESEAU_CHALEUR => false,
            default => null,
        };
    }

    /**
     * @deprecated
     */
    public function combustion_applicable(): bool
    {
        return \in_array($this, [
            self::CHAUDIERE,
            self::POELE_BOUILLEUR,
            self::RADIATEUR_GAZ,
            self::GENERATEUR_AIR_CHAUD,
            self::PAC_HYBRIDE_AIR_EAU,
            self::PAC_HYBRIDE_EAU_EAU,
            self::PAC_HYBRIDE_EAU_GLYCOLEE_EAU,
            self::PAC_HYBRIDE_GEOTHERMIQUE,
        ]);
    }

    /**
     * @deprecated
     */
    public function scop_applicable(): bool
    {
        return $this->is_pac() && $this !== self::PAC_MULTI_BATIMENT;
    }

    /**
     * @deprecated
     */
    public function tfonc_applicable(): bool
    {
        return \in_array($this, [
            self::CHAUDIERE,
            self::PAC_HYBRIDE_AIR_EAU,
            self::PAC_HYBRIDE_EAU_EAU,
            self::PAC_HYBRIDE_EAU_GLYCOLEE_EAU,
            self::PAC_HYBRIDE_GEOTHERMIQUE,
        ]);
    }
}
