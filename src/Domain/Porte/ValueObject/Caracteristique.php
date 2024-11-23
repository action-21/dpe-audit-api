<?php

namespace App\Domain\Porte\ValueObject;

use App\Domain\Common\Service\Assert;
use App\Domain\Porte\Enum\{EtatIsolation, NatureMenuiserie, TypePose, TypeVitrage};
use App\Domain\Porte\Porte;

final class Caracteristique
{
    public function __construct(
        public readonly float $surface,
        public readonly EtatIsolation $isolation,
        public readonly NatureMenuiserie $nature_menuiserie,
        public readonly TypePose $type_pose,
        public readonly int $taux_vitrage,
        public readonly ?int $largeur_dormant,
        public readonly bool $presence_sas,
        public readonly bool $presence_joint,
        public readonly bool $presence_retour_isolation,
        public readonly ?int $annee_installation,
        public readonly ?TypeVitrage $type_vitrage,
        public readonly ?float $u,
    ) {}

    public static function create_porte_vitree(
        float $surface,
        EtatIsolation $isolation,
        NatureMenuiserie $nature_menuiserie,
        TypePose $type_pose,
        int $taux_vitrage,
        TypeVitrage $type_vitrage,
        int $largeur_dormant,
        bool $presence_sas,
        bool $presence_joint,
        bool $presence_retour_isolation,
        ?int $annee_installation,
        ?float $u,
    ): self {
        return $taux_vitrage === 0
            ? self::create_porte_pleine(
                surface: $surface,
                isolation: $isolation,
                nature_menuiserie: $nature_menuiserie,
                type_pose: $type_pose,
                largeur_dormant: $largeur_dormant,
                presence_sas: $presence_sas,
                presence_joint: $presence_joint,
                presence_retour_isolation: $presence_retour_isolation,
                annee_installation: $annee_installation,
                u: $u,
            )
            : new self(
                surface: $surface,
                isolation: $isolation,
                nature_menuiserie: $nature_menuiserie,
                type_pose: $type_pose,
                taux_vitrage: $taux_vitrage,
                largeur_dormant: $largeur_dormant,
                presence_sas: $presence_sas,
                presence_joint: $presence_joint,
                presence_retour_isolation: $presence_retour_isolation,
                type_vitrage: $type_vitrage,
                annee_installation: $annee_installation,
                u: $u,
            );
    }

    public static function create_porte_pleine(
        EtatIsolation $isolation,
        NatureMenuiserie $nature_menuiserie,
        TypePose $type_pose,
        float $surface,
        ?int $largeur_dormant,
        bool $presence_sas,
        bool $presence_joint,
        bool $presence_retour_isolation,
        ?int $annee_installation,
        ?float $u,
    ): self {
        return new self(
            surface: $surface,
            isolation: $isolation,
            nature_menuiserie: $nature_menuiserie,
            type_pose: $type_pose,
            largeur_dormant: $largeur_dormant,
            presence_sas: $presence_sas,
            taux_vitrage: 0,
            type_vitrage: null,
            presence_joint: $presence_joint,
            presence_retour_isolation: $presence_retour_isolation,
            annee_installation: $annee_installation,
            u: $u,
        );
    }

    public function controle(Porte $entity): void
    {
        Assert::positif($this->surface);
        Assert::positif_ou_zero($this->taux_vitrage);
        Assert::inferieur_ou_egal_a($this->taux_vitrage, 60);
        Assert::positif($this->largeur_dormant);
        Assert::positif($this->u);
        Assert::annee($this->annee_installation);
        Assert::superieur_ou_egal_a($this->annee_installation, $entity->enveloppe()->annee_construction_batiment());
    }
}
