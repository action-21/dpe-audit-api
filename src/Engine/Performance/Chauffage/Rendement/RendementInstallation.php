<?php

namespace App\Engine\Performance\Chauffage\Rendement;

use App\Domain\Audit\Audit;
use App\Domain\Chauffage\Entity\Installation;
use App\Domain\Chauffage\Service\ChauffageTableValeurRepository;
use App\Domain\Common\Enum\ZoneClimatique;
use App\Domain\Common\ValueObject\Pourcentage;
use App\Engine\Performance\Scenario\ScenarioClimatique;
use App\Engine\Performance\Rule;

final class RendementInstallation extends Rule
{
    private Audit $audit;
    private Installation $installation;

    public function __construct(private readonly ChauffageTableValeurRepository $table_repository) {}

    /**
     * @see \App\Engine\Performance\Scenario\ScenarioClimatique::zone_climatique()
     */
    public function zone_climatique(): ZoneClimatique
    {
        return $this->audit->data()->zone_climatique;
    }

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
            zone_climatique: $this->zone_climatique(),
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

    public static function dependencies(): array
    {
        return [ScenarioClimatique::class];
    }
}
