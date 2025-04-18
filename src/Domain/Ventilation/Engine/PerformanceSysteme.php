<?php

namespace App\Domain\Ventilation\Engine;

use App\Domain\Audit\Audit;
use App\Domain\Common\EngineRule;
use App\Domain\Ventilation\Entity\Systeme;
use App\Domain\Ventilation\Service\VentilationTableValeurRepository;

final class PerformanceSysteme extends EngineRule
{
    private Systeme $systeme;

    public function __construct(
        private readonly VentilationTableValeurRepository $table_repository
    ) {}

    /**
     * Ratio du temps d'utilisation du mode mécanique
     */
    public function ratio_utilisation(): float
    {
        if (null === $value = $this->table_repository->ratio_utilisation(
            type_ventilation: $this->systeme->type(),
            type_generateur: $this->systeme->generateur()?->type(),
            type_vmc: $this->systeme->generateur()?->type_vmc(),
            generateur_collectif: $this->systeme->generateur()?->generateur_collectif(),
            annee_installation: $this->systeme->generateur()?->annee_installation(),
        )) {
            throw new \DomainException('Valeur forfaitaire "ratio_utilisation" non trouvée');
        }
        return $value;
    }

    /**
     * Puissance moyenne des auxiliaires exprimée en W
     * 
     * TODO: cas des immeubles collectifs - cf partie 5 méthode 3CLDPE
     */
    public function pvent_moy(): float
    {
        if (null === $value = $this->table_repository->pvent_moy(
            type_ventilation: $this->systeme->type(),
            type_generateur: $this->systeme->generateur()?->type(),
            type_vmc: $this->systeme->generateur()?->type_vmc(),
            generateur_collectif: $this->systeme->generateur()?->generateur_collectif(),
            annee_installation: $this->systeme->generateur()?->annee_installation(),
        )) {
            throw new \DomainException('Valeur forfaitaire "pvent_moy" non trouvée');
        }
        return $value;
    }

    /**
     * Puissance des auxiliaires exprimée en W/(m³/h)
     */
    public function pvent(): float
    {
        if (null === $value = $this->table_repository->pvent(
            type_ventilation: $this->systeme->type(),
            type_generateur: $this->systeme->generateur()?->type(),
            type_vmc: $this->systeme->generateur()?->type_vmc(),
            generateur_collectif: $this->systeme->generateur()?->generateur_collectif(),
            annee_installation: $this->systeme->generateur()?->annee_installation(),
        )) {
            throw new \DomainException('Valeur forfaitaire "pvent" non trouvée');
        }
        return $value;
    }

    /**
     * Débit volumique conventionnel à reprendre exprimé en m3/(h.m²)
     */
    public function qvarep_conv(): float
    {
        if (null === $value = $this->table_repository->qvarep_conv(
            type_ventilation: $this->systeme->type(),
            type_generateur: $this->systeme->generateur()?->type(),
            type_vmc: $this->systeme->generateur()?->type_vmc(),
            presence_echangeur_thermique: $this->systeme->generateur()?->presence_echangeur_thermique(),
            generateur_collectif: $this->systeme->generateur()?->generateur_collectif(),
            annee_installation: $this->systeme->generateur()?->annee_installation(),
        )) {
            throw new \DomainException('Valeur forfaitaire "qvarep_conv" non trouvée');
        }
        return $value;
    }

    /**
     * Débit volumique conventionnel à souffler exprimé en m3/(h.m²)
     */
    public function qvasouf_conv(): float
    {
        if (null === $value = $this->table_repository->qvasouf_conv(
            type_ventilation: $this->systeme->type(),
            type_generateur: $this->systeme->generateur()?->type(),
            type_vmc: $this->systeme->generateur()?->type_vmc(),
            presence_echangeur_thermique: $this->systeme->generateur()?->presence_echangeur_thermique(),
            generateur_collectif: $this->systeme->generateur()?->generateur_collectif(),
            annee_installation: $this->systeme->generateur()?->annee_installation(),
        )) {
            throw new \DomainException('Valeur forfaitaire "qvasouf_conv" non trouvée');
        }
        return $value;
    }

    /**
     * Somme des modules d’entrée d'air sous 20 Pa par unité de surface habitable
     * exprimée en m3/(h.m²)
     */
    public function smea_conv(): float
    {
        if (null === $value = $this->table_repository->smea_conv(
            type_ventilation: $this->systeme->type(),
            type_generateur: $this->systeme->generateur()?->type(),
            type_vmc: $this->systeme->generateur()?->type_vmc(),
            presence_echangeur_thermique: $this->systeme->generateur()?->presence_echangeur_thermique(),
            generateur_collectif: $this->systeme->generateur()?->generateur_collectif(),
            annee_installation: $this->systeme->generateur()?->annee_installation(),
        )) {
            throw new \DomainException('Valeur forfaitaire "smea_conv" non trouvée');
        }
        return $value;
    }

    public function apply(Audit $entity): void
    {
        foreach ($entity->ventilation()->systemes() as $systeme) {
            $this->systeme = $systeme;
            $systeme->calcule($systeme->data()->with(
                qvarep_conv: $this->qvarep_conv(),
                qvasouf_conv: $this->qvasouf_conv(),
                smea_conv: $this->smea_conv(),
                ratio_utilisation: $this->ratio_utilisation(),
                pvent_moy: $this->pvent_moy(),
                pvent: $this->pvent(),
            ));
        }
    }
}
