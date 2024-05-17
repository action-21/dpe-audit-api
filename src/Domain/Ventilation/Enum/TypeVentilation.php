<?php

namespace App\Domain\Ventilation\Enum;

use App\Domain\Common\Enum\Enum;

/**
 * TODO: Définir la compatibilité des types de ventilation en fonction du type d'installation (individuel ou collectif)
 */
enum TypeVentilation: int implements Enum
{
    case OUVERTURE_FENETRES = 1;
    case ENTREES_AIR_HAUTES_ET_BASSES = 2;
    case VMC_SIMPLE_FLUX_AUTOREGLABLE = 3;
    case VMC_SIMPLE_FLUX_HYGROREGLABLE_TYPE_A = 4;
    case VMC_SIMPLE_FLUX_GAZ = 5;
    case VMC_SIMPLE_FLUX_HYGROREGLABLE_TYPE_B = 6;
    case VMC_BASSE_PRESSION_AUTOREGLABLE = 7;
    case VMC_BASSE_PRESSION_HYGROREGLABLE_TYPE_A = 8;
    case VMC_BASSE_PRESSION_HYGROREGLABLE_TYPE_B = 9;
    case VMC_DOUBLE_FLUX_AVEC_ECHANGEUR = 10;
    case VMC_DOUBLE_FLUX_SANS_ECHANGEUR = 11;
    case VENTILATION_NATURELLE_SUR_CONDUIT = 12;
    case VENTILATION_HYBRIDE = 13;
    case VENTILATION_HYBRIDE_AVEC_ENTREE_AIR_HYGROREGLABLE = 14;
    case VENTILATION_MECANIQUE_SUR_CONDUIT = 15;
    case VENTILATION_NATURELLE_PAR_CONDUIT_AVEC_ENTREES_AIR_HYGROREGLABLE = 16;
    case PUIT_CLIMATIQUE_SANS_ECHANGEUR = 17;
    case PUIT_CLIMATIQUE_AVEC_ECHANGEUR = 18;
    case VMC_INSUFFLATION = 19;

    public static function from_enum_type_ventilation_id(int $id): self
    {
        return match ($id) {
            1 => self::OUVERTURE_FENETRES,
            2 => self::ENTREES_AIR_HAUTES_ET_BASSES,
            3, 4, 5, 6 => self::VMC_SIMPLE_FLUX_AUTOREGLABLE,
            7, 8, 9 => self::VMC_SIMPLE_FLUX_HYGROREGLABLE_TYPE_A,
            10, 11, 12 => self::VMC_SIMPLE_FLUX_GAZ,
            13, 14, 15 => self::VMC_SIMPLE_FLUX_HYGROREGLABLE_TYPE_B,
            16 => self::VMC_BASSE_PRESSION_AUTOREGLABLE,
            17, 18 => self::VMC_BASSE_PRESSION_HYGROREGLABLE_TYPE_A,
            19, 20, 21, 22 => self::VMC_DOUBLE_FLUX_AVEC_ECHANGEUR,
            23, 24 => self::VMC_DOUBLE_FLUX_SANS_ECHANGEUR,
            25 => self::VENTILATION_NATURELLE_SUR_CONDUIT,
            26, 27, 28 => self::VENTILATION_HYBRIDE,
            29, 30, 31 => self::VENTILATION_HYBRIDE_AVEC_ENTREE_AIR_HYGROREGLABLE,
            32, 33 => self::VENTILATION_MECANIQUE_SUR_CONDUIT,
            34 => self::VENTILATION_NATURELLE_PAR_CONDUIT_AVEC_ENTREES_AIR_HYGROREGLABLE,
            35, 36, 37, 38 => self::PUIT_CLIMATIQUE_SANS_ECHANGEUR,
        };
    }

    public function id(): int
    {
        return $this->value;
    }

    public function lib(): string
    {
        return match ($this) {
            self::OUVERTURE_FENETRES => "Ventilation par ouverture des fenêtres",
            self::ENTREES_AIR_HAUTES_ET_BASSES => "Ventilation par entrées d'air hautes et basses",
            self::VMC_SIMPLE_FLUX_AUTOREGLABLE => "VMC Simple Flux Auto réglable",
            self::VMC_SIMPLE_FLUX_HYGROREGLABLE_TYPE_A => "VMC Simple Flux Hygro A",
            self::VMC_SIMPLE_FLUX_GAZ => "VMC Simple Flux Gaz",
            self::VMC_SIMPLE_FLUX_HYGROREGLABLE_TYPE_B => "VMC Simple Flux Hygro B",
            self::VMC_BASSE_PRESSION_AUTOREGLABLE => "VMC Basse pression Auto-réglable",
            self::VMC_BASSE_PRESSION_HYGROREGLABLE_TYPE_A => "VMC Basse pression Hygro A",
            self::VMC_BASSE_PRESSION_HYGROREGLABLE_TYPE_B => "VMC Basse pression Hygro B",
            self::VMC_DOUBLE_FLUX_AVEC_ECHANGEUR => "VMC Double Flux avec échangeur",
            self::VMC_DOUBLE_FLUX_SANS_ECHANGEUR => "VMC Double Flux sans échangeur",
            self::VENTILATION_NATURELLE_SUR_CONDUIT => "Ventilation naturelle par conduit",
            self::VENTILATION_HYBRIDE => "Ventilation hybride",
            self::VENTILATION_HYBRIDE_AVEC_ENTREE_AIR_HYGROREGLABLE => "Ventilation hybride avec entrées d'air hygro",
            self::VENTILATION_MECANIQUE_SUR_CONDUIT => "Ventilation mécanique sur conduit existant",
            self::VENTILATION_NATURELLE_PAR_CONDUIT_AVEC_ENTREES_AIR_HYGROREGLABLE => "Ventilation naturelle par conduit avec entrées d'air hygro",
            self::PUIT_CLIMATIQUE_SANS_ECHANGEUR => "Puits climatique sans échangeur",
            self::PUIT_CLIMATIQUE_AVEC_ECHANGEUR => "Puits climatique avec échangeur",
            self::VMC_INSUFFLATION => "VMC par insufflation",
        };
    }

    public function ventilation_naturelle(): bool
    {
        return \in_array($this, [
            self::OUVERTURE_FENETRES,
            self::ENTREES_AIR_HAUTES_ET_BASSES,
            self::VENTILATION_NATURELLE_SUR_CONDUIT,
            self::VENTILATION_NATURELLE_PAR_CONDUIT_AVEC_ENTREES_AIR_HYGROREGLABLE,
        ]);
    }

    public function ventilation_mecanique(): bool
    {
        return !$this->ventilation_naturelle();
    }

    public function ventilation_double_flux(): bool
    {
        return \in_array($this, [self::VMC_DOUBLE_FLUX_AVEC_ECHANGEUR, self::VMC_DOUBLE_FLUX_SANS_ECHANGEUR]);
    }

    public function ventilation_hybride(): bool
    {
        return \in_array($this, [self::VENTILATION_HYBRIDE, self::VENTILATION_HYBRIDE_AVEC_ENTREE_AIR_HYGROREGLABLE]);
    }

    public function calcule_caux(): bool
    {
        return !\in_array($this, [
            self::OUVERTURE_FENETRES,
            self::ENTREES_AIR_HAUTES_ET_BASSES,
            self::VENTILATION_NATURELLE_SUR_CONDUIT,
            self::VENTILATION_MECANIQUE_SUR_CONDUIT,
            self::VENTILATION_NATURELLE_PAR_CONDUIT_AVEC_ENTREES_AIR_HYGROREGLABLE,
            self::PUIT_CLIMATIQUE_SANS_ECHANGEUR,
            self::PUIT_CLIMATIQUE_AVEC_ECHANGEUR,
        ]);
    }
}
