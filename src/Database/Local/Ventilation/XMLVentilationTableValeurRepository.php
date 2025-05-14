<?php

namespace App\Database\Local\Ventilation;

use App\Domain\Ventilation\Enum\{TypeVentilation, TypeGenerateur, TypeVmc};
use App\Domain\Common\ValueObject\Annee;
use App\Domain\Ventilation\Service\VentilationTableValeurRepository;
use App\Database\Local\{XMLTableDatabase, XMLTableElement};

final class XMLVentilationTableValeurRepository implements VentilationTableValeurRepository
{
    public function __construct(private readonly XMLTableDatabase $db) {}

    private function fetch_pvent(
        TypeVentilation $type_ventilation,
        ?TypeGenerateur $type_generateur,
        ?TypeVmc $type_vmc,
        ?bool $generateur_collectif,
        ?Annee $annee_installation
    ): ?XMLTableElement {
        return $this->db->repository('ventilation.pvent')
            ->createQuery()
            ->and('type_ventilation', $type_ventilation)
            ->and('type_generateur', $type_generateur)
            ->and('type_vmc', $type_vmc)
            ->and('generateur_collectif', $generateur_collectif)
            ->andCompareTo('annee_installation', $annee_installation?->value())
            ->getOne();
    }

    private function fetch_debit(
        TypeVentilation $type_ventilation,
        ?TypeGenerateur $type_generateur,
        ?TypeVmc $type_vmc,
        ?bool $presence_echangeur_thermique,
        ?bool $generateur_collectif,
        ?Annee $annee_installation
    ): ?XMLTableElement {
        return $this->db->repository('ventilation.debit')
            ->createQuery()
            ->and('type_ventilation', $type_ventilation)
            ->and('type_generateur', $type_generateur)
            ->and('type_vmc', $type_vmc)
            ->and('presence_echangeur_thermique', $presence_echangeur_thermique)
            ->and('generateur_collectif', $generateur_collectif)
            ->andCompareTo('annee_installation', $annee_installation?->value())
            ->getOne();
    }

    public function ratio_utilisation(
        TypeVentilation $type_ventilation,
        ?TypeGenerateur $type_generateur,
        ?TypeVmc $type_vmc,
        ?bool $generateur_collectif,
        ?Annee $annee_installation
    ): ?float {
        return $this->fetch_pvent(
            type_ventilation: $type_ventilation,
            type_generateur: $type_generateur,
            type_vmc: $type_vmc,
            generateur_collectif: $generateur_collectif,
            annee_installation: $annee_installation
        )?->floatval('ratio_utilisation');
    }

    public function pvent_moy(
        TypeVentilation $type_ventilation,
        ?TypeGenerateur $type_generateur,
        ?TypeVmc $type_vmc,
        ?bool $generateur_collectif,
        ?Annee $annee_installation
    ): ?float {
        return $this->fetch_pvent(
            type_ventilation: $type_ventilation,
            type_generateur: $type_generateur,
            type_vmc: $type_vmc,
            generateur_collectif: $generateur_collectif,
            annee_installation: $annee_installation
        )?->floatval('pvent_moy');
    }

    public function pvent(
        TypeVentilation $type_ventilation,
        ?TypeGenerateur $type_generateur,
        ?TypeVmc $type_vmc,
        ?bool $generateur_collectif,
        ?Annee $annee_installation
    ): ?float {
        return $this->fetch_pvent(
            type_ventilation: $type_ventilation,
            type_generateur: $type_generateur,
            type_vmc: $type_vmc,
            generateur_collectif: $generateur_collectif,
            annee_installation: $annee_installation
        )?->floatval('pvent');
    }

    public function qvarep_conv(
        TypeVentilation $type_ventilation,
        ?TypeGenerateur $type_generateur,
        ?TypeVmc $type_vmc,
        ?bool $presence_echangeur_thermique,
        ?bool $generateur_collectif,
        ?Annee $annee_installation
    ): ?float {
        return $this->fetch_debit(
            type_ventilation: $type_ventilation,
            type_generateur: $type_generateur,
            type_vmc: $type_vmc,
            presence_echangeur_thermique: $presence_echangeur_thermique,
            generateur_collectif: $generateur_collectif,
            annee_installation: $annee_installation
        )?->floatval('qvarep_conv');
    }

    public function qvasouf_conv(
        TypeVentilation $type_ventilation,
        ?TypeGenerateur $type_generateur,
        ?TypeVmc $type_vmc,
        ?bool $presence_echangeur_thermique,
        ?bool $generateur_collectif,
        ?Annee $annee_installation
    ): ?float {
        return $this->fetch_debit(
            type_ventilation: $type_ventilation,
            type_generateur: $type_generateur,
            type_vmc: $type_vmc,
            presence_echangeur_thermique: $presence_echangeur_thermique,
            generateur_collectif: $generateur_collectif,
            annee_installation: $annee_installation
        )?->floatval('qvasouf_conv');
    }

    public function smea_conv(
        TypeVentilation $type_ventilation,
        ?TypeGenerateur $type_generateur,
        ?TypeVmc $type_vmc,
        ?bool $presence_echangeur_thermique,
        ?bool $generateur_collectif,
        ?Annee $annee_installation
    ): ?float {
        return $this->fetch_debit(
            type_ventilation: $type_ventilation,
            type_generateur: $type_generateur,
            type_vmc: $type_vmc,
            presence_echangeur_thermique: $presence_echangeur_thermique,
            generateur_collectif: $generateur_collectif,
            annee_installation: $annee_installation
        )?->floatval('smea_conv');
    }
}
