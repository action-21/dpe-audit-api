<?php

namespace App\Domain\Ecs\Engine\Dimensionnement;

use App\Domain\Audit\Audit;
use App\Domain\Chauffage\Engine\Dimensionnement\PuissanceChauffage;
use App\Domain\Common\EngineRule;
use App\Domain\Common\ValueObject\Annee;
use App\Domain\Ecs\Entity\Generateur;
use App\Domain\Ecs\Service\EcsTableValeurRepository;

final class DimensionnementGenerateur extends EngineRule
{
    private Audit $audit;
    private Generateur $generateur;

    public function __construct(private readonly EcsTableValeurRepository $table_repository,) {}

    /**
     * @see \App\Domain\Chauffage\Engine\PuissanceChauffage::pch()
     */
    public function pch(): ?float
    {
        if (null === $this->generateur->position()->generateur_mixte_id) {
            return null;
        }
        return $this->audit->chauffage()->generateurs()
            ->find($this->generateur->position()->generateur_mixte_id)
            ->data()
            ->pch;
    }

    /**
     * @see \App\Domain\Ecs\Engine\PuissanceEcs::pecs()
     */
    public function pecs(): float
    {
        return $this->generateur->data()->pecs;
    }

    public function annee_installation(): Annee
    {
        return $this->generateur->annee_installation() ?? $this->audit->batiment()->annee_construction;
    }

    /**
     * Puissance de dimensionnement du générateur exprimée en kW
     */
    public function pdim(): float
    {
        return ($pch = $this->pch()) ? max($pch, $this->pecs()) : $this->pecs();
    }

    /**
     * Puissance nominale conventionnelle exprimée en kW
     */
    public function pn(): float
    {
        return $this->get("pn", function () {
            if (null !== $this->generateur->signaletique()->pn) {
                return $this->generateur->signaletique()->pn;
            }
            if (false === $this->generateur->type()->is_chaudiere()) {
                return $this->pecs();
            }
            if ($this->generateur->position()->generateur_multi_batiment) {
                return $this->pecs();
            }
            if (null === $pn = $this->table_repository->pn(
                type_chaudiere: $this->generateur->signaletique()->type_chaudiere,
                annee_installation_generateur: $this->annee_installation(),
                pdim: $this->pdim(),
            )) {
                throw new \DomainException('Valeur forfaitaire Pn non trouvée');
            }
            return $pn;
        });
    }

    public function apply(Audit $entity): void
    {
        $this->audit = $entity;

        foreach ($entity->ecs()->generateurs() as $generateur) {
            $this->generateur = $generateur;
            $this->clear();

            $generateur->calcule($generateur->data()->with(
                pn: $this->pn(),
            ));
        }
    }

    public static function dependencies(): array
    {
        return [
            PuissanceEcs::class,
            PuissanceChauffage::class,
        ];
    }
}
