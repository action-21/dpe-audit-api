<?php

namespace App\Domain\Chauffage\Service\MoteurRendement;

use App\Domain\Chauffage\Data\RdRepository;
use App\Domain\Chauffage\Entity\{Emetteur, Systeme};
use App\Domain\Chauffage\Enum\{IsolationReseau, TemperatureDistribution, TypeDistribution};

final class MoteurRendementDistribution
{
    public function __construct(private RdRepository $rd_repository,) {}

    public function calcule_rendement_distribution(Systeme $entity): float
    {
        if ($entity->emetteurs()->count() === 0) {
            return $this->rd(
                type_distribution: null,
                temperature_distribution: null,
                isolation_reseau: null,
                reseau_collectif: null,
            );
        }
        return \array_reduce($entity->emetteurs()->values(), fn(float $carry, Emetteur $item): float => $carry += $this->rd(
            type_distribution: $entity->reseau()->type_distribution,
            temperature_distribution: $item->temperature_distribution(),
            isolation_reseau: $entity->reseau()->isolation_reseau,
            reseau_collectif: $entity->generateur()->generateur_collectif(),
        ), 0) / $entity->emetteurs()->count();
    }

    public function rd(
        ?TypeDistribution $type_distribution,
        ?TemperatureDistribution $temperature_distribution,
        ?IsolationReseau $isolation_reseau,
        ?bool $reseau_collectif,
    ): float {
        if (null === $data = $this->rd_repository->find_by(
            type_distribution: $type_distribution,
            temperature_distribution: $temperature_distribution,
            isolation_reseau: $isolation_reseau,
            reseau_collectif: $reseau_collectif,
        )) throw new \DomainException("Valeur forfaitaire Rd non trouvée.");

        return $data->rd;
    }
}
