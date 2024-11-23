<?php

namespace App\Domain\Baie\ValueObject;

use App\Domain\Baie\Enum\{NatureGazLame, NatureMenuiserie, TypePose, TypeVitrage};
use App\Domain\Baie\Enum\TypeVitrage\TypeVitrageComplexe;
use App\Domain\Common\Service\Assert;

final class Menuiserie
{
    public function __construct(
        public readonly NatureMenuiserie $nature,
        public readonly TypeVitrage $type_vitrage,
        public readonly TypePose $type_pose,
        public readonly bool $presence_joint,
        public readonly bool $presence_retour_isolation,
        public readonly ?int $largeur_dormant,
        public readonly ?Survitrage $survitrage = null,
        public readonly ?bool $presence_rupteur_pont_thermique = null,
        public readonly ?NatureGazLame $nature_gaz_lame = null,
        public readonly ?int $epaisseur_lame = null,
    ) {}

    public static function create_menuiserie_vitrage_simple(
        NatureMenuiserie $nature,
        TypePose $type_pose,
        bool $presence_joint,
        bool $presence_retour_isolation,
        int $largeur_dormant,
        ?Survitrage $survitrage,
        ?bool $presence_rupteur_pont_thermique
    ): self {
        return new self(
            type_vitrage: TypeVitrage::SIMPLE_VITRAGE,
            nature: $nature,
            type_pose: $type_pose,
            presence_joint: $presence_joint,
            presence_retour_isolation: $presence_retour_isolation,
            largeur_dormant: $largeur_dormant,
            presence_rupteur_pont_thermique: $presence_rupteur_pont_thermique,
            survitrage: $survitrage,
        );
    }

    public static function create_menuiserie_vitrage_complexe(
        TypeVitrageComplexe $type_vitrage,
        NatureMenuiserie $nature,
        TypePose $type_pose,
        NatureGazLame $nature_gaz_lame,
        bool $presence_joint,
        bool $presence_retour_isolation,
        int $largeur_dormant,
        ?int $epaisseur_lame,
        ?bool $presence_rupteur_pont_thermique
    ): self {
        return new self(
            type_vitrage: $type_vitrage->to(),
            nature: $nature,
            type_pose: $type_pose,
            nature_gaz_lame: $nature_gaz_lame,
            presence_joint: $presence_joint,
            presence_retour_isolation: $presence_retour_isolation,
            largeur_dormant: $largeur_dormant,
            epaisseur_lame: $epaisseur_lame,
            presence_rupteur_pont_thermique: $presence_rupteur_pont_thermique,
        );
    }

    public function controle(): void
    {
        Assert::positif($this->largeur_dormant);
        Assert::positif($this->epaisseur_lame);
    }
}
