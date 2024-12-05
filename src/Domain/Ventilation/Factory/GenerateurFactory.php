<?php

namespace App\Domain\Ventilation\Factory;

use App\Domain\Common\Type\Id;
use App\Domain\Ventilation\Entity\Generateur;
use App\Domain\Ventilation\Enum\{TypeGenerateur, TypeVmc};
use App\Domain\Ventilation\Ventilation;

final class GenerateurFactory
{
    private Id $id;
    private Ventilation $ventilation;
    private string $description;
    private ?int $annee_installation;

    public function initialise(Id $id, Ventilation $ventilation, string $description, ?int $annee_installation): self
    {
        $this->id = $id;
        $this->ventilation = $ventilation;
        $this->description = $description;
        $this->annee_installation = $annee_installation;
        return $this;
    }

    private function build(
        TypeGenerateur $type,
        ?TypeVmc $type_vmc = null,
        bool $presence_echangeur_thermique = false,
        bool $generateur_collectif = false,
    ): Generateur {
        $entity = new Generateur(
            id: $this->id,
            ventilation: $this->ventilation,
            description: $this->description,
            type: $type,
            type_vmc: $type_vmc,
            annee_installation: $this->annee_installation,
            presence_echangeur_thermique: $presence_echangeur_thermique,
            generateur_collectif: $generateur_collectif,
        );
        $entity->controle();
        return $entity;
    }

    public function build_vmc_simple_flux(TypeVmc $type_vmc, bool $generateur_collectif,): Generateur
    {
        return $this->build(
            type: TypeGenerateur::VMC_SIMPLE_FLUX,
            type_vmc: $type_vmc,
            generateur_collectif: $generateur_collectif,
        );
    }

    public function build_vmc_simple_flux_gaz(?TypeVmc $type_vmc, bool $generateur_collectif,): Generateur
    {
        return $this->build(
            type: TypeGenerateur::VMC_SIMPLE_FLUX_GAZ,
            type_vmc: $type_vmc,
            generateur_collectif: $generateur_collectif,
        );
    }

    public function build_vmc_hybride(TypeVmc $type_vmc, bool $generateur_collectif,): Generateur
    {
        return $this->build(
            type: TypeGenerateur::VMC_SIMPLE_FLUX_GAZ,
            type_vmc: $type_vmc,
            generateur_collectif: $generateur_collectif,
        );
    }

    public function build_vmc_basse_pression(TypeVmc $type_vmc, bool $generateur_collectif,): Generateur
    {
        return $this->build(
            type: TypeGenerateur::VMC_SIMPLE_FLUX_GAZ,
            type_vmc: $type_vmc,
            generateur_collectif: $generateur_collectif,
        );
    }

    public function build_vmc_insufflation(bool $generateur_collectif,): Generateur
    {
        return $this->build(
            type: TypeGenerateur::VMC_INSUFFLATION,
            generateur_collectif: $generateur_collectif,
        );
    }

    public function build_vmc_double_flux(bool $presence_echangeur_thermique, bool $generateur_collectif,): Generateur
    {
        return $this->build(
            type: TypeGenerateur::VMC_DOUBLE_FLUX,
            presence_echangeur_thermique: $presence_echangeur_thermique,
            generateur_collectif: $generateur_collectif,
        );
    }

    public function build_puit_climatique(bool $presence_echangeur_thermique, bool $generateur_collectif,): Generateur
    {
        return $this->build(
            type: TypeGenerateur::PUIT_CLIMATIQUE,
            presence_echangeur_thermique: $presence_echangeur_thermique,
            generateur_collectif: $generateur_collectif,
        );
    }

    public function build_ventilation_mecanique(bool $generateur_collectif,): Generateur
    {
        return $this->build(
            type: TypeGenerateur::VENTILATION_MECANIQUE,
            generateur_collectif: $generateur_collectif,
        );
    }

    public function build_vmr(): Generateur
    {
        return $this->build(
            type: TypeGenerateur::VMR,
        );
    }
}
