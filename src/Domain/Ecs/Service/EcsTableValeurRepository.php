<?php

namespace App\Domain\Ecs\Service;

use App\Domain\Audit\Enum\TypeBatiment;
use App\Domain\Common\Enum\ZoneClimatique;
use App\Domain\Common\ValueObject\{Annee, Pourcentage};
use App\Domain\Ecs\Enum\{BouclageReseau, EnergieGenerateur, LabelGenerateur, TypeChaudiere, ModeCombustion, TypeGenerateur, UsageEcs};

interface EcsTableValeurRepository
{
    public function pn(
        TypeChaudiere $type_chaudiere,
        Annee $annee_installation_generateur,
        float $pdim,
    ): ?float;

    public function paux(
        TypeGenerateur $type_generateur,
        EnergieGenerateur $energie_generateur,
        bool $presence_ventouse,
        float $pn,
    ): ?float;

    public function rd(
        bool $production_volume_habitable,
        bool $reseau_collectif,
        bool $alimentation_contigue,
        ?BouclageReseau $bouclage_reseau,
    ): ?float;

    public function rg(
        TypeGenerateur $type_generateur,
        EnergieGenerateur $energie_generateur,
    ): ?float;

    public function cr(
        TypeGenerateur $type_generateur,
        float $volume_stockage,
        ?LabelGenerateur $label_generateur,
    ): ?float;

    public function cop(
        ZoneClimatique $zone_climatique,
        TypeGenerateur $type_generateur,
        Annee $annee_installation,
    ): ?float;

    public function fecs(
        ZoneClimatique $zone_climatique,
        TypeBatiment $type_batiment,
        UsageEcs $usage_solaire,
        Annee $annee_installation,
    ): ?Pourcentage;

    public function rpn(
        TypeGenerateur $type_generateur,
        EnergieGenerateur $energie_generateur,
        ModeCombustion $mode_combustion,
        Annee $annee_installation,
        float $pn,
    ): ?Pourcentage;

    public function qp0(
        TypeGenerateur $type_generateur,
        EnergieGenerateur $energie_generateur,
        ModeCombustion $mode_combustion,
        Annee $annee_installation,
        float $pn,
        float $e,
        float $f,
    ): ?float;

    public function pveilleuse(
        TypeGenerateur $type_generateur,
        EnergieGenerateur $energie_generateur,
        ModeCombustion $mode_combustion,
        Annee $annee_installation,
    ): ?float;
}
