<?php

namespace App\Domain\Baie\ValueObject;

use App\Domain\Baie\Enum\{NatureGazLame, NatureMenuiserie, TypeBaie, TypePose, TypeVitrage};

final class DoubleFenetre
{
    public function __construct(
        public readonly TypeBaie $type_baie,
        public readonly TypePose $type_pose,
        public readonly NatureMenuiserie $nature_menuiserie,
        public readonly TypeVitrage $type_vitrage,
        public readonly InclinaisonVitrage $inclinaison_vitrage,
        public readonly ?EpaisseurLameAir $epaisseur_lame,
        public readonly ?NatureGazLame $nature_gaz_lame,
        public readonly ?Ug $ug = null,
        public readonly ?Uw $uw = null,
        public readonly ?Sw $sw = null,
    ) {
    }

    public static function create(
        TypeBaie $type_baie,
        TypePose $type_pose,
        NatureMenuiserie $nature_menuiserie,
        TypeVitrage $type_vitrage,
        InclinaisonVitrage $inclinaison_vitrage,
        ?EpaisseurLameAir $epaisseur_lame,
        ?NatureGazLame $nature_gaz_lame,
        ?Ug $ug = null,
        ?Uw $uw = null,
        ?Sw $sw = null,
    ): self {
        if ($nature_menuiserie === NatureMenuiserie::BRIQUE_VERRE) {
            $type_vitrage = TypeVitrage::BRIQUE_VERRE;
        }
        if ($nature_menuiserie === NatureMenuiserie::POLYCARBONATE) {
            $type_vitrage = TypeVitrage::POLYCARBONATE;
        }
        if (false === $type_vitrage->epaisseur_lame_air_applicable()) {
            $epaisseur_lame = null;
        }
        if (false === $type_vitrage->nature_gaz_lame_applicable()) {
            $nature_gaz_lame = null;
        }
        return new self(
            type_baie: $type_baie,
            type_pose: $type_pose,
            nature_menuiserie: $nature_menuiserie,
            type_vitrage: $type_vitrage,
            inclinaison_vitrage: $inclinaison_vitrage,
            epaisseur_lame: $epaisseur_lame,
            nature_gaz_lame: $nature_gaz_lame,
            ug: $ug,
            uw: $uw,
            sw: $sw,
        );
    }
}
