<?php

namespace App\Domain\Ventilation\Service;

use App\Domain\Common\ValueObject\Annee;
use App\Domain\Ventilation\Enum\{TypeGenerateur, TypeVentilation, TypeVmc};

interface VentilationTableValeurRepository
{
    public function ratio_utilisation(
        TypeVentilation $type_ventilation,
        ?TypeGenerateur $type_generateur,
        ?TypeVmc $type_vmc,
        ?bool $generateur_collectif,
        ?Annee $annee_installation,
    ): ?float;

    public function pvent_moy(
        TypeVentilation $type_ventilation,
        ?TypeGenerateur $type_generateur,
        ?TypeVmc $type_vmc,
        ?bool $generateur_collectif,
        ?Annee $annee_installation,
    ): ?float;

    public function pvent(
        TypeVentilation $type_ventilation,
        ?TypeGenerateur $type_generateur,
        ?TypeVmc $type_vmc,
        ?bool $generateur_collectif,
        ?Annee $annee_installation,
    ): ?float;

    public function qvarep_conv(
        TypeVentilation $type_ventilation,
        ?TypeGenerateur $type_generateur,
        ?TypeVmc $type_vmc,
        ?bool $presence_echangeur_thermique,
        ?bool $generateur_collectif,
        ?Annee $annee_installation,
    ): ?float;

    public function qvasouf_conv(
        TypeVentilation $type_ventilation,
        ?TypeGenerateur $type_generateur,
        ?TypeVmc $type_vmc,
        ?bool $presence_echangeur_thermique,
        ?bool $generateur_collectif,
        ?Annee $annee_installation,
    ): ?float;

    public function smea_conv(
        TypeVentilation $type_ventilation,
        ?TypeGenerateur $type_generateur,
        ?TypeVmc $type_vmc,
        ?bool $presence_echangeur_thermique,
        ?bool $generateur_collectif,
        ?Annee $annee_installation,
    ): ?float;
}
