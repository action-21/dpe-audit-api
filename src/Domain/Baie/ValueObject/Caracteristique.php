<?php

namespace App\Domain\Baie\ValueObject;

use App\Domain\Baie\Enum\{NatureGazLame, TypeBaie, TypeFermeture, NatureMenuiserie, TypePose, TypeVitrage};

final class Caracteristique
{
    public function __construct(
        public readonly bool $presence_joint,
        public readonly bool $presence_retour_isolation,
        public readonly Surface $surface,
        public readonly LargeurDormant $largeur_dormant,
        public readonly TypeBaie $type_baie,
        public readonly TypePose $type_pose,
        public readonly NatureMenuiserie $nature_menuiserie,
        public readonly InclinaisonVitrage $inclinaison_vitrage,
        public readonly TypeFermeture $type_fermeture,
        public readonly ?TypeVitrage $type_vitrage,
        public readonly ?EpaisseurLameAir $epaisseur_lame,
        public readonly ?NatureGazLame $nature_gaz_lame,
        public readonly ?Ug $ug = null,
        public readonly ?Uw $uw = null,
        public readonly ?Ujn $ujn = null,
        public readonly ?Sw $sw = null,
    ) {
    }

    public static function create(
        bool $presence_joint,
        bool $presence_retour_isolation,
        Surface $surface,
        LargeurDormant $largeur_dormant,
        TypeBaie $type_baie,
        NatureMenuiserie $nature_menuiserie,
        InclinaisonVitrage $inclinaison_vitrage,
        TypeFermeture $type_fermeture,
        TypeVitrage $type_vitrage,
        ?TypePose $type_pose = null,
        ?EpaisseurLameAir $epaisseur_lame,
        ?NatureGazLame $nature_gaz_lame,
        ?Ug $ug = null,
        ?Uw $uw = null,
        ?Ujn $ujn = null,
        ?Sw $sw = null,
    ): self {
        $cases_nature_menuiserie = NatureMenuiserie::cases_by_type_baie($type_baie);
        $cases_type_pose = TypePose::cases_by_type_baie($type_baie);
        $cases_type_vitrage = TypeVitrage::cases_by_nature_menuiserie($nature_menuiserie);

        if (\count($cases_nature_menuiserie) === 1) {
            $nature_menuiserie = \reset($cases_nature_menuiserie);
        }
        if (\count($cases_type_vitrage) === 1) {
            $type_vitrage = \reset($cases_type_vitrage);
        }
        if (\count($cases_type_pose) === 1) {
            $type_pose = \reset($cases_type_pose);
        }
        if (\count($cases_type_pose) === 0) {
            $type_pose = null;
        }
        if (false === NatureGazLame::is_applicable_by_type_vitrage($type_vitrage)) {
            $nature_gaz_lame = null;
        }
        if (false === EpaisseurLameAir::is_requis_by_type_vitrage($type_vitrage)) {
            $epaisseur_lame = null;
        }
        if ($type_fermeture === TypeFermeture::SANS_FERMETURE) {
            $ujn = null;
        }
        if (!\in_array($type_pose, $cases_type_pose)) {
            throw new \InvalidArgumentException("Le type de pose n'est pas applicable à la baie");
        }
        if (!\in_array($type_vitrage, $cases_type_vitrage)) {
            throw new \InvalidArgumentException("Le type de vitrage n'est pas applicable à la baie");
        }
        if (null === $nature_gaz_lame && NatureGazLame::is_requis_by_type_vitrage($type_vitrage)) {
            throw new \InvalidArgumentException("La nature du gaz de la lame d'air est requise pour ce type de vitrage");
        }
        if (null === $epaisseur_lame && EpaisseurLameAir::is_requis_by_type_vitrage($type_vitrage)) {
            throw new \InvalidArgumentException("L'épaisseur de la lame d'air est requise pour ce type de vitrage");
        }
        return new self(
            surface: $surface,
            presence_joint: $presence_joint,
            presence_retour_isolation: $presence_retour_isolation,
            largeur_dormant: $largeur_dormant,
            type_baie: $type_baie,
            type_pose: $type_pose,
            nature_menuiserie: $nature_menuiserie,
            inclinaison_vitrage: $inclinaison_vitrage,
            type_fermeture: $type_fermeture,
            type_vitrage: $type_vitrage,
            epaisseur_lame: $epaisseur_lame,
            nature_gaz_lame: $nature_gaz_lame,
            ug: $ug,
            uw: $uw,
            ujn: $ujn,
            sw: $sw,
        );
    }

    public function epaisseur_lame_air(): float
    {
        if ($this->epaisseur_lame) {
            return $this->epaisseur_lame->valeur();
        }
        return $this->type_vitrage->epaisseur_lame_air_applicable() ? 6 : 0;
    }
}
