<?php

namespace App\Domain\Chauffage\Engine\Rendement;

use App\Domain\Audit\Audit;
use App\Domain\Chauffage\Entity\Systeme;
use App\Domain\Chauffage\Enum\IsolationReseau;
use App\Domain\Chauffage\Service\ChauffageTableValeurRepository;
use App\Domain\Common\EngineRule;

final class RendementDistribution extends EngineRule
{
    protected Systeme $systeme;

    public function __construct(protected readonly ChauffageTableValeurRepository $table_repository) {}

    /**
     * Rendement de distribution
     */
    public function rd(): float
    {
        if ($this->systeme->emetteurs()->count() === 0) {
            return 1;
        }
        /** @var float[] */
        $values = [];

        foreach ($this->systeme->emetteurs() as $emetteur) {
            if (null === $rd = $this->table_repository->rd(
                type_distribution: $this->systeme->reseau()->type_distribution,
                temperature_distribution: $emetteur->temperature_distribution(),
                isolation_reseau: $this->systeme->reseau()->isolation ?? IsolationReseau::NON_ISOLE,
                reseau_collectif: $this->systeme->generateur()->position()->generateur_collectif,
            )) {
                throw new \DomainException('Valeur forfaitaire Rd non trouvée');
            }
            $values[] = $rd;
        }
        return array_sum($values) / count($values);
    }

    public function apply(Audit $entity): void
    {
        foreach ($entity->chauffage()->systemes() as $systeme) {
            $this->systeme = $systeme;

            $systeme->calcule($systeme->data()->with(
                rd: $this->rd(),
            ));
        }
    }
}
