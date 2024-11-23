<?php

namespace App\Domain\Refroidissement\Service;

use App\Domain\Common\Enum\{ScenarioUsage, Usage};
use App\Domain\Common\ValueObject\ConsommationCollection;
use App\Domain\Refroidissement\Entity\Systeme;

/**
 * @uses \App\Domain\Refroidissement\Service\MoteurBesoin
 * @uses \App\Domain\Refroidissement\Service\MoteurPerformance
 */
final class MoteurConsommation
{
    public function __construct(private MoteurDimensionnement $moteur_dimensionnement,) {}

    public function calcule_consommations(Systeme $entity): ConsommationCollection
    {
        $besoins = $entity->installation()->refroidissement()->besoins();
        $eer = $entity->generateur()->performance()->eer;
        $rdim = $this->moteur_dimensionnement->calcule_dimensionnement($entity);

        return ConsommationCollection::create(
            usage: Usage::REFROIDISSEMENT,
            energie: $entity->generateur()->energie()->to(),
            callback: fn(ScenarioUsage $scenario): float => $this->cfr(
                bfr: $besoins->besoins(scenario: $scenario),
                eer: $eer,
                rdim: $rdim,
            )
        );
    }

    /**
     * Consommation de refroidissement en kWh
     * 
     * @param float $bfr - Besoin de refroidissement en kWh
     * @param float $eer - Coefficient d'efficience énergétique
     * @param float $rdim - Ratio de dimensionnement (installation x système)
     */
    public function cfr(float $bfr, float $eer, float $rdim): float
    {
        return 0.9 * ($bfr / $eer) * $rdim;
    }
}
