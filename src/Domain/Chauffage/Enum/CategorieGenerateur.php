<?php

namespace App\Domain\Chauffage\Enum;

use App\Domain\Common\Enum\Enum;
use App\Domain\Lnc\Data\T;

enum CategorieGenerateur: string implements Enum
{
    case CHAUDIERE_BASSE_TEMPERATURE = 'CHAUDIERE_BASSE_TEMPERATURE';
    case CHAUDIERE_BOIS = 'CHAUDIERE_BOIS';
    case CHAUDIERE_CONDENSATION = 'CHAUDIERE_CONDENSATION';
    case CHAUDIERE_ELECTRIQUE = 'CHAUDIERE_ELECTRIQUE';
    case CHAUDIERE_MULTI_BATIMENT = 'CHAUDIERE_MULTI_BATIMENT';
    case CHAUDIERE_STANDARD = 'CHAUDIERE_STANDARD';
    case CHAUFFAGE_ELECTRIQUE = 'CHAUFFAGE_ELECTRIQUE';
    case GENERATEUR_AIR_CHAUD = 'GENERATEUR_AIR_CHAUD';
    case PAC = 'PAC';
    case PAC_HYBRIDE = 'PAC_HYBRIDE';
    case PAC_MULTI_BATIMENT = 'PAC_MULTI_BATIMENT';
    case POELE_BOIS_BOUILLEUR = 'POELE_BOIS_BOUILLEUR';
    case POELE_INSERT = 'POELE_INSERT';
    case RADIATEUR_GAZ = 'RADIATEUR_GAZ';
    case RESEAU_CHALEUR = 'RESEAU_CHALEUR';

    public static function determine(TypeGenerateur $type_generateur, EnergieGenerateur $energie_generateur): self
    {
        return match ($type_generateur) {
            TypeGenerateur::SYSTEME_COLLECTIF_PAR_DEFAUT => self::CHAUDIERE_STANDARD,
            TypeGenerateur::CHAUDIERE_STANDARD => match ($energie_generateur) {
                EnergieGenerateur::BOIS_BUCHE,
                EnergieGenerateur::BOIS_GRANULE,
                EnergieGenerateur::BOIS_PLAQUETTE => self::CHAUDIERE_BOIS,
                EnergieGenerateur::ELECTRICITE => self::CHAUDIERE_ELECTRIQUE,
                default => self::CHAUDIERE_STANDARD,
            },
            TypeGenerateur::CHAUDIERE_BASSE_TEMPERATURE => match ($energie_generateur) {
                EnergieGenerateur::BOIS_BUCHE,
                EnergieGenerateur::BOIS_GRANULE,
                EnergieGenerateur::BOIS_PLAQUETTE => self::CHAUDIERE_BOIS,
                default => self::CHAUDIERE_BASSE_TEMPERATURE,
            },
            TypeGenerateur::CHAUDIERE_CONDENSATION => match ($energie_generateur) {
                EnergieGenerateur::BOIS_BUCHE,
                EnergieGenerateur::BOIS_GRANULE,
                EnergieGenerateur::BOIS_PLAQUETTE => self::CHAUDIERE_BOIS,
                default => self::CHAUDIERE_CONDENSATION,
            },
            TypeGenerateur::GENERATEUR_AIR_CHAUD => match ($energie_generateur) {
                EnergieGenerateur::ELECTRICITE => self::CHAUFFAGE_ELECTRIQUE,
                default => self::GENERATEUR_AIR_CHAUD,
            },
            TypeGenerateur::CONVECTEUR_BI_JONCTION,
            TypeGenerateur::CONVECTEUR_ELECTRIQUE,
            TypeGenerateur::PANNEAU_RAYONNANT_ELECTRIQUE,
            TypeGenerateur::PLAFOND_RAYONNANT_ELECTRIQUE,
            TypeGenerateur::PLANCHER_RAYONNANT_ELECTRIQUE,
            TypeGenerateur::RADIATEUR_ELECTRIQUE,
            TypeGenerateur::RADIATEUR_ELECTRIQUE_ACCUMULATION,
            TypeGenerateur::GENERATEUR_AIR_CHAUD => self::CHAUFFAGE_ELECTRIQUE,
            TypeGenerateur::PAC_AIR_AIR,
            TypeGenerateur::PAC_AIR_EAU,
            TypeGenerateur::PAC_EAU_EAU,
            TypeGenerateur::PAC_EAU_GLYCOLEE_EAU,
            TypeGenerateur::PAC_GEOTHERMIQUE => self::PAC,
            TypeGenerateur::PAC_HYBRIDE_AIR_EAU,
            TypeGenerateur::PAC_HYBRIDE_EAU_EAU,
            TypeGenerateur::PAC_HYBRIDE_EAU_GLYCOLEE_EAU,
            TypeGenerateur::PAC_HYBRIDE_GEOTHERMIQUE => self::PAC_HYBRIDE,
            TypeGenerateur::CUISINIERE,
            TypeGenerateur::FOYER_FERME,
            TypeGenerateur::INSERT,
            TypeGenerateur::POELE => self::POELE_INSERT,
            TypeGenerateur::POELE_BOUILLEUR => self::POELE_BOIS_BOUILLEUR,
            TypeGenerateur::RADIATEUR_INDEPENDANT => self::RADIATEUR_GAZ,
            TypeGenerateur::RESEAU_CHALEUR => self::RESEAU_CHALEUR,
            TypeGenerateur::CHAUDIERE_MULTI_BATIMENT,
            TypeGenerateur::PAC_MULTI_BATIMENT => self::PAC_MULTI_BATIMENT,
        };
    }

    public function id(): string
    {
        return $this->value;
    }

    public function lib(): string
    {
        return match ($this) {
            self::PAC => 'Pompe à chaleur',
            self::PAC_HYBRIDE => 'Pompe à chaleur hybride',
            self::PAC_MULTI_BATIMENT => 'Pompe à chaleur multi bâtiment',
            self::CHAUDIERE_BOIS => 'Chaudière bois',
            self::CHAUDIERE_ELECTRIQUE => 'Chaudière electrique',
            self::CHAUDIERE_STANDARD => 'Chaudière standard',
            self::CHAUDIERE_BASSE_TEMPERATURE => 'Chaudière basse temperature',
            self::CHAUDIERE_CONDENSATION => 'Chaudière condensation',
            self::CHAUDIERE_MULTI_BATIMENT => 'Chaudière multi bâtiment',
            self::CHAUFFAGE_ELECTRIQUE => 'Chauffage electrique',
            self::GENERATEUR_AIR_CHAUD => 'Générateur à air chaud',
            self::POELE_BOIS_BOUILLEUR => 'Poêle à bois bouilleur',
            self::POELE_INSERT => 'Poêles et inserts',
            self::RADIATEUR_GAZ => 'Radiateur à gaz',
            self::RESEAU_CHALEUR => 'Reseau de chaleur',
        };
    }

    public function effet_joule(): bool
    {
        return $this === self::CHAUFFAGE_ELECTRIQUE;
    }

    public function is_pac(): bool
    {
        return \in_array($this, [
            self::PAC,
            self::PAC_HYBRIDE,
            self::PAC_MULTI_BATIMENT,
        ]);
    }

    public function is_chaudiere(): bool
    {
        return \in_array($this, [
            self::CHAUDIERE_ELECTRIQUE,
            self::CHAUDIERE_STANDARD,
            self::CHAUDIERE_BASSE_TEMPERATURE,
            self::CHAUDIERE_CONDENSATION,
            self::CHAUDIERE_MULTI_BATIMENT,
        ]);
    }

    public function is_chaudiere_bois(): bool
    {
        return \in_array($this, [
            self::CHAUDIERE_BOIS,
        ]);
    }

    public function is_systeme_central(): bool
    {
        return false === \in_array($this, [
            self::CHAUFFAGE_ELECTRIQUE,
            self::POELE_INSERT,
            self::RADIATEUR_GAZ,
        ]);
    }

    public function is_systeme_divise(): bool
    {
        return \in_array($this, [
            self::CHAUFFAGE_ELECTRIQUE,
            self::PAC,
            self::POELE_INSERT,
            self::RADIATEUR_GAZ,
        ]);
    }

    public function combustion(): bool
    {
        return \in_array($this, [
            self::GENERATEUR_AIR_CHAUD,
            self::CHAUDIERE_BASSE_TEMPERATURE,
            self::CHAUDIERE_BOIS,
            self::CHAUDIERE_CONDENSATION,
            self::CHAUDIERE_STANDARD,
            self::POELE_BOIS_BOUILLEUR,
            self::RADIATEUR_GAZ,
        ]);
    }

    /** @return TypeDistribution[] */
    public function types_distribution(?TypeGenerateur $type_generateur = null): array
    {
        return match ($this) {
            self::CHAUDIERE_BOIS,
            self::CHAUDIERE_BASSE_TEMPERATURE,
            self::CHAUDIERE_CONDENSATION,
            self::CHAUDIERE_STANDARD,
            self::CHAUDIERE_MULTI_BATIMENT,
            self::CHAUDIERE_ELECTRIQUE,
            self::POELE_BOIS_BOUILLEUR => [
                TypeDistribution::AERAULIQUE,
                TypeDistribution::HYDRAULIQUE,
            ],
            self::GENERATEUR_AIR_CHAUD => [
                TypeDistribution::AERAULIQUE,
            ],
            self::PAC_HYBRIDE,
            self::PAC_MULTI_BATIMENT,
            self::RESEAU_CHALEUR => [
                TypeDistribution::AERAULIQUE,
                TypeDistribution::FLUIDE_FRIGORIGENE,
                TypeDistribution::HYDRAULIQUE,
            ],
            self::PAC => match ($type_generateur) {
                TypeGenerateur::PAC_AIR_AIR => [
                    TypeDistribution::SANS,
                    TypeDistribution::AERAULIQUE,
                    TypeDistribution::FLUIDE_FRIGORIGENE
                ],
                default => [
                    TypeDistribution::AERAULIQUE,
                    TypeDistribution::FLUIDE_FRIGORIGENE,
                    TypeDistribution::HYDRAULIQUE,
                ],
            },
            self::CHAUFFAGE_ELECTRIQUE,
            self::POELE_INSERT,
            self::RADIATEUR_GAZ => [TypeDistribution::SANS]
        };
    }
}
