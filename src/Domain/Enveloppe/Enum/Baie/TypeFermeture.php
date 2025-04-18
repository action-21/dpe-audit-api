<?php

namespace App\Domain\Enveloppe\Enum\Baie;

use App\Domain\Common\Enum\Enum;

enum TypeFermeture: string implements Enum
{
    case SANS_FERMETURE = 'sans_fermeture';
    case JALOUSIE_ACCORDEON = 'jalousie_accordeon';
    case FERMETURE_LAMES_ORIENTABLES = 'fermeture_lames_orientables';
    case VENITIENS_EXTERIEURS_METAL = 'venitiens_exterieurs_metal';
    case VOLET_BATTANT_AVEC_AJOURS_FIXES = 'volet_battant_avec_ajours_fixes';
    case PERSIENNES_AVEC_AJOURS_FIXES = 'persiennes_avec_ajours_fixes';
    case FERMETURE_SANS_AJOURS = 'fermeture_sans_ajours';
    case VOLETS_ROULANTS_ALUMINIUM = 'volets_roulants_aluminium';
    case VOLETS_ROULANTS_PVC_BOIS_EPAISSEUR_LTE_12MM = 'volets_roulants_pvc_bois_epaisseur_lte_12mm';
    case VOLETS_ROULANTS_PVC_BOIS_EPAISSEUR_GT_12MM = 'volets_roulants_pvc_bois_epaisseur_gt_12mm';
    case PERSIENNE_COULISSANTE_EPAISSEUR_LTE_22MM = 'persienne_coulissante_epaisseur_lte_22mm';
    case PERSIENNE_COULISSANTE_EPAISSEUR_GT_22MM = 'persienne_coulissante_epaisseur_gt_22mm';
    case VOLET_BATTANT_PVC_BOIS_EPAISSEUR_LTE_22MM = 'volet_battant_pvc_bois_epaisseur_lte_22mm';
    case VOLET_BATTANT_PVC_BOIS_EPAISSEUR_GT_22MM = 'volet_battant_pvc_bois_epaisseur_gt_22mm';
    case FERMETURE_ISOLEE_SANS_AJOURS = 'fermeture_isolee_sans_ajours';

    public static function from_enum_type_fermeture_id(int $id): self
    {
        return match ($id) {
            1 => self::SANS_FERMETURE,
            2 => self::VOLET_BATTANT_AVEC_AJOURS_FIXES,
            3 => self::FERMETURE_LAMES_ORIENTABLES,
            4 => self::VOLETS_ROULANTS_PVC_BOIS_EPAISSEUR_LTE_12MM,
            5 => self::VOLET_BATTANT_PVC_BOIS_EPAISSEUR_LTE_22MM,
            6 => self::VOLETS_ROULANTS_PVC_BOIS_EPAISSEUR_GT_12MM,
            7 => self::VOLET_BATTANT_PVC_BOIS_EPAISSEUR_GT_22MM,
            8 => self::FERMETURE_ISOLEE_SANS_AJOURS,
        };
    }

    public function id(): string
    {
        return $this->value;
    }

    public function lib(): string
    {
        return match ($this) {
            self::SANS_FERMETURE => 'Sans fermeture',
            self::JALOUSIE_ACCORDEON => 'Jalousie accordéon',
            self::FERMETURE_LAMES_ORIENTABLES => 'Fermeture à lames orientables',
            self::VENITIENS_EXTERIEURS_METAL => 'Vénitiens extérieurs tout métal',
            self::VOLET_BATTANT_AVEC_AJOURS_FIXES => 'Volet battant avec ajours fixes',
            self::PERSIENNES_AVEC_AJOURS_FIXES => 'Persiennes avec ajours fixes',
            self::FERMETURE_SANS_AJOURS => 'Fermeture sans ajours en position déployée',
            self::VOLETS_ROULANTS_ALUMINIUM => 'Volet roulant aluminium',
            self::VOLETS_ROULANTS_PVC_BOIS_EPAISSEUR_LTE_12MM => 'Volet roulant PVC ou bois d\'épaisseur inférieure ou égale à 12mm',
            self::VOLETS_ROULANTS_PVC_BOIS_EPAISSEUR_GT_12MM => 'Volet roulant PVC ou bois d\'épaisseur supérieure à 12mm',
            self::PERSIENNE_COULISSANTE_EPAISSEUR_LTE_22MM => 'Persienne coulissante d\'épaisseur inférieure ou égale à 22mm',
            self::PERSIENNE_COULISSANTE_EPAISSEUR_GT_22MM => 'Persienne coulissante d\'épaisseur supérieure à 22mm',
            self::VOLET_BATTANT_PVC_BOIS_EPAISSEUR_LTE_22MM => 'Volet battant PVC ou bois d\'épaisseur inférieure ou égale à 22mm',
            self::VOLET_BATTANT_PVC_BOIS_EPAISSEUR_GT_22MM => 'Volet battant PVC ou bois d\'épaisseur supérieure à 22mm',
            self::FERMETURE_ISOLEE_SANS_AJOURS => 'Fermeture isolée sans ajours en position déployée',
        };
    }
}
