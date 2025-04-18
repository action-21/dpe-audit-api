<?php

namespace App\Domain\Chauffage\Engine\Rendement;

use App\Domain\Audit\Audit;
use App\Domain\Common\EngineRule;
use App\Domain\Chauffage\Entity\Installation;
use App\Domain\Chauffage\Service\ChauffageTableValeurRepository;
use App\Domain\Common\ValueObject\Pourcentage;

final class RendementInstallation extends EngineRule
{
    private Audit $audit;
    private Installation $installation;

    public function __construct(private readonly ChauffageTableValeurRepository $table_repository) {}

    /**
     * Facteur de couverture solaire
     */
    public function fch(): Pourcentage
    {
        if (null === $this->installation->solaire_thermique()) {
            return Pourcentage::from(0);
        }
        if ($this->installation->solaire_thermique()->fch) {
            return $this->installation->solaire_thermique()->fch;
        }
        if (null === $fch = $this->table_repository->fch(
            zone_climatique: $this->audit->adresse()->zone_climatique,
            type_batiment: $this->audit->batiment()->type,
        )) {
            throw new \DomainException("Valeurs forfaitaires Fch non trouvées");
        }
        return $fch;
    }

    public function apply(Audit $entity): void
    {
        $this->audit = $entity;

        foreach ($entity->chauffage()->installations() as $installation) {
            $this->installation = $installation;
            $installation->calcule($installation->data()->with(
                fch: $this->fch()
            ));
        }
    }
}
