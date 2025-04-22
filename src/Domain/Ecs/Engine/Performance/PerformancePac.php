<?php

namespace App\Domain\Ecs\Engine\Performance;

use App\Domain\Audit\Audit;
use App\Domain\Common\EngineRule;
use App\Domain\Common\ValueObject\Annee;
use App\Domain\Ecs\Entity\Generateur;
use App\Domain\Ecs\Service\EcsTableValeurRepository;

final class PerformancePac extends EngineRule
{
    private Audit $audit;
    private Generateur $generateur;

    public function __construct(private readonly EcsTableValeurRepository $table_repository,) {}

    /**
     * Année d'installation
     */
    public function annee_installation(): ?Annee
    {
        return $this->generateur->annee_installation() ?? $this->audit->batiment()->annee_construction;
    }

    /**
     * Coefficient de performance énergétique
     */
    public function cop(): float
    {
        return $this->get("cop", function () {
            if ($this->generateur->signaletique()->cop) {
                return $this->generateur->signaletique()->cop;
            }
            if (null === $cop = $this->table_repository->cop(
                type_generateur: $this->generateur->type(),
                zone_climatique: $this->audit->adresse()->zone_climatique,
                annee_installation: $this->annee_installation(),
            )) {
                throw new \DomainException("Valeurs forfaitaires COP non trouvées");
            }
            return $cop;
        });
    }

    public function apply(Audit $entity): void
    {
        $this->audit = $entity;

        foreach ($entity->ecs()->generateurs() as $generateur) {
            if (false === $generateur->type()->is_pac()) {
                continue;
            }
            if ($generateur->position()->generateur_multi_batiment) {
                continue;
            }
            $this->generateur = $generateur;
            $this->clear();

            $generateur->calcule($generateur->data()->with(
                cop: $this->cop(),
            ));
        }
    }
}
