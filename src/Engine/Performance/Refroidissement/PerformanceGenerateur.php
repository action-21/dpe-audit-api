<?php

namespace App\Engine\Performance\Refroidissement;

use App\Domain\Audit\Audit;
use App\Domain\Common\Enum\ZoneClimatique;
use App\Domain\Common\ValueObject\Annee;
use App\Domain\Refroidissement\Entity\Generateur;
use App\Domain\Refroidissement\Service\RefroidissementTableValeurRepository;
use App\Engine\Performance\Rule;
use App\Engine\Performance\Scenario\ScenarioClimatique;

final class PerformanceGenerateur extends Rule
{
    private Audit $audit;
    private Generateur $generateur;

    public function __construct(private readonly RefroidissementTableValeurRepository $table_repository) {}

    /**
     * @see \App\Engine\Performance\Scenario\ScenarioClimatique::zone_climatique()
     */
    public function zone_climatique(): ZoneClimatique
    {
        return $this->audit->data()->zone_climatique;
    }

    public function annee_installation(): Annee
    {
        return $this->generateur->annee_installation() ?? $this->audit->batiment()->annee_construction;
    }

    /**
     * Coefficient d'efficience énergétique
     */
    public function eer(): float
    {
        return $this->get('eer', function () {
            if ($this->generateur->seer()) {
                return $this->generateur->seer();
            }
            if (null === $eer = $this->table_repository->eer(
                zone_climatique: $this->zone_climatique(),
                annee_installation_generateur: $this->annee_installation(),
            )) {
                throw new \DomainException('Valeur forfaitaire EER non trouvé');
            }
            return $eer;
        });
    }

    public function apply(Audit $entity): void
    {
        $this->audit = $entity;

        foreach ($entity->refroidissement()->generateurs() as $generateur) {
            $this->clear();
            $this->generateur = $generateur;

            $generateur->calcule($generateur->data()->with(
                eer: $this->eer(),
            ));
        }
    }

    public static function dependencies(): array
    {
        return [ScenarioClimatique::class];
    }
}
