<?php

namespace App\Domain\Ecs\Enum;

use App\Domain\Common\Enum\Enum;

enum CategorieGenerateur: string implements Enum
{
    case PAC = 'PAC';
    case PAC_MULTI_BATIMENT = 'PAC_MULTI_BATIMENT';
    case CHAUDIERE_BOIS = 'CHAUDIERE_BOIS';
    case CHAUDIERE_ELECTRIQUE = 'CHAUDIERE_ELECTRIQUE';
    case CHAUDIERE_STANDARD = 'CHAUDIERE_STANDARD';
    case CHAUDIERE_BASSE_TEMPERATURE = 'CHAUDIERE_BASSE_TEMPERATURE';
    case CHAUDIERE_CONDENSATION = 'CHAUDIERE_CONDENSATION';
    case CHAUDIERE_MULTI_BATIMENT = 'CHAUDIERE_MULTI_BATIMENT';
    case POELE_BOIS_BOUILLEUR = 'POELE_BOIS_BOUILLEUR';
    case RESEAU_CHALEUR = 'RESEAU_CHALEUR';
    case CHAUFFE_EAU_THERMODYNAMIQUE = 'CHAUFFE_EAU_THERMODYNAMIQUE';
    case ACCUMULATEUR = 'ACCUMULATEUR';
    case CHAUFFE_EAU_ELECTRIQUE = 'CHAUFFE_EAU_ELECTRIQUE';
    case CHAUFFE_EAU_INSTANTANE = 'CHAUFFE_EAU_INSTANTANE';

    public static function determine(TypeGenerateur $type, EnergieGenerateur $energie): self
    {
        return match ($type) {
            TypeGenerateur::ACCUMULATEUR_STANDARD,
            TypeGenerateur::ACCUMULATEUR_CONDENSATION => self::ACCUMULATEUR,

            TypeGenerateur::PAC_MULTI_BATIMENT => self::PAC_MULTI_BATIMENT,
            TypeGenerateur::CHAUDIERE_MULTI_BATIMENT => self::CHAUDIERE_MULTI_BATIMENT,

            TypeGenerateur::SYSTEME_COLLECTIF_PAR_DEFAUT => self::CHAUDIERE_STANDARD,

            TypeGenerateur::CHAUDIERE_STANDARD => match ($energie) {
                EnergieGenerateur::BOIS,
                EnergieGenerateur::BOIS_BUCHE,
                EnergieGenerateur::BOIS_GRANULE,
                EnergieGenerateur::BOIS_PLAQUETTE => self::CHAUDIERE_BOIS,
                EnergieGenerateur::ELECTRICITE => self::CHAUDIERE_ELECTRIQUE,
                default => self::CHAUDIERE_STANDARD
            },
            TypeGenerateur::CHAUDIERE_BASSE_TEMPERATURE => match ($energie) {
                EnergieGenerateur::BOIS,
                EnergieGenerateur::BOIS_BUCHE,
                EnergieGenerateur::BOIS_GRANULE,
                EnergieGenerateur::BOIS_PLAQUETTE => self::CHAUDIERE_BOIS,
                default => self::CHAUDIERE_BASSE_TEMPERATURE
            },
            TypeGenerateur::CHAUDIERE_CONDENSATION => match ($energie) {
                EnergieGenerateur::BOIS,
                EnergieGenerateur::BOIS_BUCHE,
                EnergieGenerateur::BOIS_GRANULE,
                EnergieGenerateur::BOIS_PLAQUETTE => self::CHAUDIERE_BOIS,
                default => self::CHAUDIERE_CONDENSATION
            },

            TypeGenerateur::CHAUFFE_EAU_INSTANTANE => match ($energie) {
                EnergieGenerateur::ELECTRICITE => self::CHAUFFE_EAU_ELECTRIQUE,
                default => self::CHAUFFE_EAU_INSTANTANE
            },

            TypeGenerateur::BALLON_ELECTRIQUE_HORIZONTAL,
            TypeGenerateur::BALLON_ELECTRIQUE_VERTICAL => self::CHAUFFE_EAU_ELECTRIQUE,

            TypeGenerateur::CET_AIR_AMBIANT,
            TypeGenerateur::CET_AIR_EXTERIEUR,
            TypeGenerateur::CET_AIR_EXTRAIT => self::CHAUFFE_EAU_THERMODYNAMIQUE,

            TypeGenerateur::PAC_DOUBLE_SERVICE => self::PAC,
            TypeGenerateur::POELE_BOUILLEUR => self::POELE_BOIS_BOUILLEUR,
            TypeGenerateur::RESEAU_CHALEUR => self::RESEAU_CHALEUR,
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
            self::PAC_MULTI_BATIMENT => 'Pompe à chaleur multi bâtiment',
            self::CHAUDIERE_BOIS => 'Chaudière bois',
            self::CHAUDIERE_ELECTRIQUE => 'Chaudière électrique',
            self::CHAUDIERE_STANDARD => 'Chaudière standard',
            self::CHAUDIERE_BASSE_TEMPERATURE => 'Chaudière basse temperature',
            self::CHAUDIERE_CONDENSATION => 'Chaudière condensation',
            self::CHAUDIERE_MULTI_BATIMENT => 'Chaudière multi bâtiment',
            self::POELE_BOIS_BOUILLEUR => 'Poêle à bois bouilleur',
            self::RESEAU_CHALEUR => 'Reseau de chaleur',
            self::CHAUFFE_EAU_THERMODYNAMIQUE => 'Chauffe-eau thermodynamique',
            self::ACCUMULATEUR => 'Accumulateur',
            self::CHAUFFE_EAU_ELECTRIQUE => 'Chauffe-eau électrique',
            self::CHAUFFE_EAU_INSTANTANE => 'Chauffe-eau instantané'
        };
    }
}
