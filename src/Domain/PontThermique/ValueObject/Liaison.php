<?php

namespace App\Domain\PontThermique\ValueObject;

use App\Domain\Common\Enum\Enum;
use App\Domain\Common\ValueObject\Id;
use App\Domain\PontThermique\Enum\TypeLiaison;

final class Liaison
{
    public function __construct(
        public readonly TypeLiaison $type_liaision,
        public readonly ?Id $mur_id = null,
        public readonly ?Id $plancher_id = null,
        public readonly ?Id $refend_id = null,
        public readonly ?Id $ouverture_id = null,
        public readonly ?Enum $type_isolation_mur = null,
        public readonly ?Enum $type_isolation_plancher = null,
        public readonly ?Enum $type_pose_ouverture = null,
        public readonly ?bool $presence_retour_isolation_ouverture = null,
        public readonly ?float $largeur_dormant_ouverture = null,
    ) {
    }

    public static function create_liaison_plancher_bas_mur(
        Id $mur_id,
        Id $plancher_id,
        Enum $type_isolation_mur,
        Enum $type_isolation_plancher,
    ): self {
        return new self(
            type_liaision: TypeLiaison::PLANCHER_BAS_MUR,
            mur_id: $mur_id,
            plancher_id: $plancher_id,
            type_isolation_mur: $type_isolation_mur,
            type_isolation_plancher: $type_isolation_plancher,
        );
    }

    public static function create_liaison_plancher_intermediaire_mur(
        Id $mur_id,
        Id $plancher_id,
        Enum $type_isolation_mur,
        Enum $type_isolation_plancher,
    ): self {
        return new self(
            type_liaision: TypeLiaison::PLANCHER_INTERMEDIAIRE_MUR,
            mur_id: $mur_id,
            plancher_id: $plancher_id,
            type_isolation_mur: $type_isolation_mur,
            type_isolation_plancher: $type_isolation_plancher,
        );
    }

    public static function create_liaison_plancher_haut_mur(
        Id $mur_id,
        Id $plancher_id,
        Enum $type_isolation_mur,
        Enum $type_isolation_plancher,
    ): self {
        return new self(
            type_liaision: TypeLiaison::PLANCHER_HAUT_MUR,
            mur_id: $mur_id,
            plancher_id: $plancher_id,
            type_isolation_mur: $type_isolation_mur,
            type_isolation_plancher: $type_isolation_plancher,
        );
    }

    public static function create_liaison_refend_mur(Id $mur_id, Id $refend_id, Enum $type_isolation_mur): self
    {
        return new self(
            type_liaision: TypeLiaison::REFEND_MUR,
            mur_id: $mur_id,
            refend_id: $refend_id,
            type_isolation_mur: $type_isolation_mur,
        );
    }

    public static function create_liaison_menuiserie_mur(
        Id $mur_id,
        Id $ouverture_id,
        Enum $type_isolation_mur,
        Enum $type_pose_ouverture,
        bool $presence_retour_isolation_ouverture,
        float $largeur_dormant_ouverture,
    ): self {
        return new self(
            type_liaision: TypeLiaison::MENUISERIE_MUR,
            mur_id: $mur_id,
            ouverture_id: $ouverture_id,
            type_isolation_mur: $type_isolation_mur,
            type_pose_ouverture: $type_pose_ouverture,
            presence_retour_isolation_ouverture: $presence_retour_isolation_ouverture,
            largeur_dormant_ouverture: $largeur_dormant_ouverture,
        );
    }
}
