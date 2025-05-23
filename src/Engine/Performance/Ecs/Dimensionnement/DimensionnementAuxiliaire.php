<?php

namespace App\Engine\Performance\Ecs\Dimensionnement;

use App\Domain\Audit\Audit;
use App\Domain\Ecs\Entity\Generateur;
use App\Domain\Ecs\Service\EcsTableValeurRepository;
use App\Engine\Performance\Ecs\Dimensionnement\DimensionnementGenerateur;
use App\Engine\Performance\Rule;

final class DimensionnementAuxiliaire extends Rule
{
    private Generateur $generateur;

    public function __construct(private readonly EcsTableValeurRepository $table_repository,) {}

    /**
     * @see \App\Engine\Performance\Ecs\Dimensionnement\DimensionnementGenerateur::pn()
     */
    public function pn(): float
    {
        return $this->generateur->data()->pn;
    }

    /**
     * Puissance des auxiliaires de génération exprimée en W
     */
    public function paux(): float
    {
        if (false === $this->generateur->type()->is_combustion()) {
            return 0;
        }
        if (false === $this->generateur->energie()->is_combustible()) {
            return 0;
        }
        if (null === $paux = $this->table_repository->paux(
            type_generateur: $this->generateur->type(),
            energie_generateur: $this->generateur->energie(),
            presence_ventouse: $this->generateur->combustion()->presence_ventouse,
            pn: $this->pn(),
        )) {
            throw new \DomainException("Valeurs forfaitaires Paux non trouvées");
        }
        return $paux;
    }

    public function apply(Audit $entity): void
    {
        foreach ($entity->ecs()->generateurs() as $generateur) {
            $this->generateur = $generateur;

            $generateur->calcule($generateur->data()->with(
                paux: $this->paux(),
            ));
        }
    }

    public static function dependencies(): array
    {
        return [
            DimensionnementGenerateur::class,
        ];
    }
}
