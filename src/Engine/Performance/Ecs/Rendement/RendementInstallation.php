<?php

namespace App\Engine\Performance\Ecs\Rendement;

use App\Domain\Audit\Audit;
use App\Domain\Common\Enum\ZoneClimatique;
use App\Domain\Common\ValueObject\Pourcentage;
use App\Domain\Ecs\Entity\Installation;
use App\Domain\Ecs\Service\EcsTableValeurRepository;
use App\Engine\Performance\Rule;
use App\Engine\Performance\Scenario\ScenarioClimatique;

final class RendementInstallation extends Rule
{
    private Audit $audit;
    private Installation $installation;

    public function __construct(private readonly EcsTableValeurRepository $table_repository) {}

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
    public function fecs(): Pourcentage
    {
        return $this->get("fecs", function () {
            if (null === $this->installation->solaire_thermique()) {
                return Pourcentage::from(0);
            }
            if ($this->installation->solaire_thermique()->fecs) {
                return $this->installation->solaire_thermique()->fecs;
            }
            if (null === $fecs = $this->table_repository->fecs(
                zone_climatique: $this->zone_climatique(),
                type_batiment: $this->audit->batiment()->type,
                usage_solaire: $this->installation->solaire_thermique()->usage,
                annee_installation: $this->installation->solaire_thermique()->annee_installation ?? $this->audit->batiment()->annee_construction,
            )) {
                throw new \DomainException("Valeurs forfaitaires Fecs non trouvées");
            }
            return $fecs;
        });
    }

    public function apply(Audit $entity): void
    {
        $this->audit = $entity;

        foreach ($entity->ecs()->installations() as $installation) {
            $this->installation = $installation;
            $this->clear();

            $installation->calcule($installation->data()->with(
                fecs: $this->fecs()
            ));
        }
    }

    public static function dependencies(): array
    {
        return [ScenarioClimatique::class];
    }
}
