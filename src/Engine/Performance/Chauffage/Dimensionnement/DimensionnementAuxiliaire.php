<?php

namespace App\Engine\Performance\Chauffage\Dimensionnement;

use App\Domain\Audit\Audit;
use App\Domain\Chauffage\Entity\Generateur;
use App\Domain\Chauffage\Service\ChauffageTableValeurRepository;
use App\Engine\Performance\Rule;

final class DimensionnementAuxiliaire extends Rule
{
    private Generateur $generateur;

    public function __construct(private readonly ChauffageTableValeurRepository $table_repository,) {}

    /**
     * @see \App\Engine\Performance\Chauffage\Dimensionnement\DimensionnementGenerateur::pn()
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
        return $this->get("paux", function () {
            if (false === $this->generateur->type()->is_combustion()) {
                return 0;
            }
            if (false === $this->generateur->energie()->is_combustible()) {
                return 0;
            }
            if (null === $paux = $this->table_repository->paux(
                type_generateur: $this->generateur->type(),
                energie_generateur: $this->generateur->energie(),
                generateur_multi_batiment: $this->generateur->position()->generateur_multi_batiment,
                presence_ventouse: $this->generateur->combustion()?->presence_ventouse ?? false,
                pn: $this->pn(),
            )) {
                throw new \DomainException("Valeurs forfaitaires Paux non trouvées");
            }
            return $paux;
        });
    }

    public function apply(Audit $entity): void
    {
        foreach ($entity->chauffage()->generateurs() as $generateur) {
            $this->generateur = $generateur;
            $this->clear();

            $generateur->calcule($generateur->data()->with(
                paux: $this->paux(),
            ));
        }
    }

    public static function dependencies(): array
    {
        return [DimensionnementGenerateur::class];
    }
}
