<?php

namespace App\Domain\Ecs\Engine\Rendement;

use App\Domain\Audit\Audit;
use App\Domain\Common\EngineRule;
use App\Domain\Common\ValueObject\Pourcentage;
use App\Domain\Ecs\Entity\Installation;
use App\Domain\Ecs\Service\EcsTableValeurRepository;

final class RendementInstallation extends EngineRule
{
    private Audit $audit;
    private Installation $installation;

    public function __construct(private readonly EcsTableValeurRepository $table_repository) {}

    /**
     * Facteur de couverture solaire
     */
    public function fecs(): Pourcentage
    {
        if (null === $this->installation->solaire_thermique()) {
            return Pourcentage::from(0);
        }
        if ($this->installation->solaire_thermique()->fecs) {
            return $this->installation->solaire_thermique()->fecs;
        }
        if (null === $fecs = $this->table_repository->fecs(
            zone_climatique: $this->audit->adresse()->zone_climatique,
            type_batiment: $this->audit->batiment()->type,
            usage_solaire: $this->installation->solaire_thermique()->usage,
            annee_installation: $this->installation->solaire_thermique()->annee_installation ?? $this->audit->batiment()->annee_construction,
        )) {
            throw new \DomainException("Valeurs forfaitaires Fecs non trouvées");
        }
        return $fecs;
    }

    public function apply(Audit $entity): void
    {
        $this->audit = $entity;

        foreach ($entity->ecs()->installations() as $installation) {
            $this->installation = $installation;
            $installation->calcule($installation->data()->with(
                fecs: $this->fecs()
            ));
        }
    }
}
