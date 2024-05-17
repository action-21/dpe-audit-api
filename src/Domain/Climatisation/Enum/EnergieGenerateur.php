<?php

namespace App\Domain\Climatisation\Enum;

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
    case RESEAU_FROID_URBAIN = 15;

    public static function from_enum_type_energie_id(int $id): self
    {
        return self::from($id);
    }

    /**
     * TODO: à vérifier
     * 
     * @return array<EnergieGenerateur>
     */
    public static function cases_by_type_generateur(TypeGenerateur $type_generateur): array
    {
        return match (true) {
            \in_array($type_generateur, [
                TypeGenerateur::PAC_AIR_AIR,
                TypeGenerateur::PAC_AIR_EAU,
                TypeGenerateur::PAC_EAU_EAU,
                TypeGenerateur::PAC_EAU_GLYCOLEE_EAU,
                TypeGenerateur::PAC_GEOTHERMIQUE,
                TypeGenerateur::AUTRE_SYSTEME_THERMODYNAMIQUE_ELECTRIQUE,
                TypeGenerateur::AUTRE,
            ]) => [
                self::ELECTRICITE,
                self::ELECTRICITE_RENOUVELABLE,
            ],
            $type_generateur === TypeGenerateur::RESEAU_FROID_URBAIN => [
                self::RESEAU_FROID_URBAIN
            ],
            $type_generateur === TypeGenerateur::AUTRE_SYSTEME_THERMODYNAMIQUE_GAZ => [
                self::GAZ_NATUREL,
                self::PROPANE,
                self::BUTANE,
                self::GPL,
                self::AUTRES_FOSSILES
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
        return Energie::from($this->id());
    }

    /**
     * Coefficient de conversion en PCI/PCS 
     */
    public function coefficient_conversion_pcs(): float
    {
        return $this->energie()->coefficient_conversion_pcs();
    }

    /**
     * Facteur de conversion en énergie primaire
     */
    public function facteur_energie_primaire(): float
    {
        return $this->energie()->facteur_energie_primaire();
    }
}
