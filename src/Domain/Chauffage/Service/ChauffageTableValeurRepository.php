<?php

namespace App\Domain\Chauffage\Service;

use App\Domain\Audit\Enum\TypeBatiment;
use App\Domain\Chauffage\Enum\{EnergieGenerateur, IsolationReseau, LabelGenerateur, ModeCombustion, TemperatureDistribution, TypeChaudiere, TypeDistribution, TypeEmission, TypeGenerateur, TypeIntermittence};
use App\Domain\Common\Enum\ZoneClimatique;
use App\Domain\Common\ValueObject\{Annee, Pourcentage};

interface ChauffageTableValeurRepository
{
    public function i0(
        TypeBatiment $type_batiment,
        TypeEmission $type_emission,
        TypeIntermittence $type_intermittence,
        bool $chauffage_central,
        bool $regulation_terminale,
        bool $chauffage_collectif,
        bool $inertie_lourde,
        bool $comptage_individuel,
    ): ?float;

    public function fch(
        ZoneClimatique $zone_climatique,
        TypeBatiment $type_batiment,
    ): ?Pourcentage;

    public function paux(
        TypeGenerateur $type_generateur,
        EnergieGenerateur $energie_generateur,
        bool $generateur_multi_batiment,
        bool $presence_ventouse,
        float $pn,
    ): ?float;

    public function pn(
        TypeChaudiere $type_chaudiere,
        Annee $annee_installation_generateur,
        float $pdim,
    ): ?float;

    public function rd(
        TypeDistribution $type_distribution,
        TemperatureDistribution $temperature_distribution,
        bool $reseau_collectif,
        ?IsolationReseau $isolation_reseau,
    ): ?float;

    public function re(
        TypeEmission $type_emission,
        TypeGenerateur $type_generateur,
        ?LabelGenerateur $label_generateur,
    ): ?float;

    public function rg(
        TypeGenerateur $type_generateur,
        EnergieGenerateur $energie_generateur,
        LabelGenerateur $label_generateur,
        Annee $anne_installation_generateur,
    ): ?float;

    public function rr(
        TypeEmission $type_emission,
        TypeGenerateur $type_generateur,
        ?LabelGenerateur $label_generateur,
        bool $reseau_collectif,
        bool $presence_regulation_terminale,
        ?bool $presence_robinet_thermostatique,
    ): ?float;

    public function scop(
        ZoneClimatique $zone_climatique,
        TypeGenerateur $type_generateur,
        TypeEmission $type_emission,
        Annee $annee_installation_generateur,
    ): ?float;

    public function rpn(
        TypeGenerateur $type_generateur,
        ModeCombustion $mode_combustion,
        EnergieGenerateur $energie_generateur,
        Annee $annee_installation_generateur,
        float $pn,
    ): ?Pourcentage;

    public function rpint(
        TypeGenerateur $type_generateur,
        ModeCombustion $mode_combustion,
        EnergieGenerateur $energie_generateur,
        Annee $annee_installation_generateur,
        float $pn,
    ): ?Pourcentage;

    public function qp0(
        TypeGenerateur $type_generateur,
        ModeCombustion $mode_combustion,
        EnergieGenerateur $energie_generateur,
        Annee $annee_installation_generateur,
        float $pn,
        float $e,
        float $f
    ): ?float;

    public function pveilleuse(
        TypeGenerateur $type_generateur,
        ModeCombustion $mode_combustion,
        EnergieGenerateur $energie_generateur,
        Annee $annee_installation_generateur,
        float $pn,
    ): ?float;

    public function tfonc30(
        TypeGenerateur $type_generateur,
        ModeCombustion $mode_combustion,
        TemperatureDistribution $temperature_distribution,
        Annee $annee_installation_emetteur,
        Annee $annee_installation_generateur,
    ): ?float;

    public function tfonc100(
        TemperatureDistribution $temperature_distribution,
        Annee $annee_installation_emetteur,
    ): ?float;
}
