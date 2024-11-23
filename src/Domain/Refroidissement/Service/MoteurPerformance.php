<?php

namespace App\Domain\Refroidissement\Service;

use App\Domain\Common\Enum\ZoneClimatique;
use App\Domain\Refroidissement\Data\EerRepository;
use App\Domain\Refroidissement\Entity\Generateur;
use App\Domain\Refroidissement\ValueObject\Performance;

final class MoteurPerformance
{
    public function __construct(private EerRepository $eer_repository) {}

    public function calcule_performance(Generateur $entity): Performance
    {
        return Performance::create(eer: $this->eer(
            zone_climatique: $entity->refroidissement()->audit()->zone_climatique(),
            annee_installation_generateur: $entity->annee_installation() ?? $entity->refroidissement()->annee_construction_batiment(),
            seer_saisi: $entity->seer()
        ));
    }

    /**
     * Coefficient d'efficience énergétique
     */
    public function eer(ZoneClimatique $zone_climatique, int $annee_installation_generateur, ?float $seer_saisi): float
    {
        if ($seer_saisi)
            return $seer_saisi * 0.95;

        if (null === $data = $this->eer_repository->find_by(
            zone_climatique: $zone_climatique,
            annee_installation_generateur: $annee_installation_generateur
        )) throw new \DomainException('Valeur forfaitaire EER non trouvé');

        return $data->eer;
    }
}
