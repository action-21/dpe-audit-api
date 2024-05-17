<?php

namespace App\Domain\Mur\Enum;

use App\Domain\Common\Enum\Enum;

enum TypeIsolation: int implements Enum
{
    case INCONNU = 1;
    case NON_ISOLE = 2;
    case ITI = 3;
    case ITE = 4;
    case ITR = 5;
    case ITI_ITE = 6;
    case ITI_ITR = 7;
    case ITE_ITR = 8;
    case ISOLE_TYPE_INCONNU = 9;

    public static function from_enum_type_isolation_id(int $id): self
    {
        return self::from($id);
    }

    /** @return array<self> */
    public static function cases_by_type_mur(TypeMur $type_mur): array
    {
        return match (true) {
            \in_array($type_mur, [
                TypeMur::PIERRE_MOELLONS,
                TypeMur::PIERRE_MOELLONS_AVEC_REMPLISSAGE,
                TypeMur::PISE_OU_BETON_TERRE,
                TypeMur::PAN_BOIS_SANS_REMPLISSAGE,
                TypeMur::PAN_BOIS_AVEC_REMPLISSAGE,
                TypeMur::BOIS_RONDIN,
                TypeMur::BRIQUE_PLEINE_SIMPLE,
                TypeMur::BRIQUE_PLEINE_DOUBLE_AVEC_LAME_AIR,
                TypeMur::BRIQUE_CREUSE,
                TypeMur::BLOC_BETON_PLEIN,
                TypeMur::BLOC_BETON_CREUX,
                TypeMur::BETON_BANCHE,
                TypeMur::BETON_MACHEFER,
                TypeMur::BRIQUE_TERRE_CUITE_ALVEOLAIRE,
                TypeMur::CLOISON_PLATRE,
                TypeMur::AUTRES_MATERIAUX_ANCIENS,
                TypeMur::OSSATURE_BOIS_SANS_REMPLISSAGE,
                TypeMur::OSSATURE_BOIS_AVEC_REMPLISSAGE_TOUT_VENANT,
            ]) => [
                self::INCONNU,
                self::NON_ISOLE,
                self::ITI,
                self::ITE,
                self::ITI_ITE,
                self::ISOLE_TYPE_INCONNU
            ],
            \in_array($type_mur, [
                TypeMur::OSSATURE_BOIS_AVEC_REMPLISSAGE_ISOLANT_AVANT_2001,
                TypeMur::OSSATURE_BOIS_AVEC_REMPLISSAGE_ISOLANT_2001_2005,
                TypeMur::OSSATURE_BOIS_AVEC_REMPLISSAGE_ISOLANT_A_PARTIR_2006,
            ]) => [
                self::ITR,
                self::ITI_ITR,
                self::ITE_ITR,
            ],
            \in_array($type_mur, [
                TypeMur::SANDWICH_BETON_ISOLANT_BETON_SANS_ISOLATION_RAPPORTEE,
            ]) => [
                self::ITR,
            ],
            default => self::cases(),
        };
    }

    public function defaut(int $annee_construction): self
    {
        if (false === $this->inconnu()) {
            return $this;
        }
        if ($this === self::ISOLE_TYPE_INCONNU) {
            return self::ITI;
        }
        return $annee_construction < 1974 ? self::NON_ISOLE : self::ITI;
    }

    public function id(): int
    {
        return $this->value;
    }

    public function lib(): string
    {
        return match ($this) {
            self::INCONNU => 'Inconnu',
            self::NON_ISOLE => 'Non isolé',
            self::ITI => 'ITI',
            self::ITE => 'ITE',
            self::ITR => 'ITR',
            self::ITI_ITE => 'ITI + ITE',
            self::ITI_ITR => 'ITI + ITR',
            self::ITE_ITR => 'ITE + ITR',
            self::ISOLE_TYPE_INCONNU => 'Isolé mais type d\'isolation inconnu'
        };
    }

    public function inconnu(): bool
    {
        return $this === self::INCONNU || $this === self::ISOLE_TYPE_INCONNU;
    }

    public function est_isole(): ?bool
    {
        return match ($this) {
            self::INCONNU => null,
            self::NON_ISOLE => false,
            default => true
        };
    }
}
