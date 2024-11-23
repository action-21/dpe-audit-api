<?php

namespace App\Domain\Ventilation\Service;

use App\Domain\Audit\Enum\TypeBatiment;
use App\Domain\Common\Enum\{Energie, ScenarioUsage, Usage};
use App\Domain\Common\ValueObject\ConsommationCollection;
use App\Domain\Ventilation\Entity\Systeme;

/**
 * @uses \App\Domain\Ventilation\Service\MoteurPerformance
 */
final class MoteurConsommation
{
    public function __construct(private MoteurDimensionnement $moteur_dimensionnement,) {}

    public function calcule_consommations(Systeme $entity): ConsommationCollection
    {
        $pvent_moy = $entity->ventilation()->audit()->type_batiment() === TypeBatiment::IMMEUBLE ? $this->pvent_moy(
            pvent: $entity->performance()->pvent,
            qvarep_conv: $entity->performance()->qvarep_conv,
            surface: $entity->installation()->surface()
        ) : $entity->performance()->pvent_moy;

        $rdim = $this->moteur_dimensionnement->calcule_dimensionnement($entity);
        $ratio_temps_utilisation = $entity->performance()->ratio_utilisation;

        return ConsommationCollection::create(
            usage: Usage::AUXILIAIRE,
            energie: Energie::ELECTRICITE,
            callback: fn(ScenarioUsage $scenario): float => $this->caux(
                pvent_moy: $pvent_moy,
                rdim: $rdim,
                ratio_temps_utilisation: $ratio_temps_utilisation,
            )
        );
    }

    /**
     * Consommation annuelle de l'auxiliaire de ventilation en kWh
     * 
     * @param float $pvent_moy - Puissance moyenne de l'auxiliaire de ventilation en W
     * @param float $rdim - Ratio de dimensionnement (installation x système)
     */
    public function caux(float $pvent_moy, float $rdim, float $ratio_temps_utilisation): float
    {
        return 8760 * ($pvent_moy / 1000) * $rdim * $ratio_temps_utilisation;
    }

    /**
     * Consommation annuelle des auxiliaires de ventilation en kWh
     * 
     * @param float $pvent_moy - Puissance moyenne des auxiliaires de ventilation en W
     * @param float $qvarep_conv - Débit volumique conventionnel à reprendre en m3/(h.m²)
     * @param float $surface - Surface couverte par l'installation en m²
     */
    public function pvent_moy(float $pvent, float $qvarep_conv, float $surface): float
    {
        return $pvent * $qvarep_conv * $surface;
    }
}
