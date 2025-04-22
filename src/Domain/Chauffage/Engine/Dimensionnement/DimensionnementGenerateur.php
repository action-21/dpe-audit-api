<?php

namespace App\Domain\Chauffage\Engine\Dimensionnement;

use App\Domain\Audit\Audit;
use App\Domain\Common\EngineRule;
use App\Domain\Chauffage\Entity\Generateur;
use App\Domain\Chauffage\Enum\TypeChaudiere;
use App\Domain\Chauffage\Service\ChauffageTableValeurRepository;
use App\Domain\Ecs\Engine\Dimensionnement\PuissanceEcs;

final class DimensionnementGenerateur extends EngineRule
{
    private Audit $audit;
    private Generateur $generateur;

    public function __construct(private readonly ChauffageTableValeurRepository $table_repository,) {}

    /**
     * @see \App\Domain\Ecs\Engine\Dimensionnement\PuissanceEcs::pecs()
     */
    public function pecs(): ?float
    {
        if (null === $this->generateur->position()->generateur_mixte_id) {
            return null;
        }
        return $this->audit->ecs()->generateurs()
            ->find($this->generateur->position()->generateur_mixte_id)
            ->data()
            ->pecs;
    }

    /**
     * @see \App\Domain\Chauffage\Engine\Dimensionnement\PuissanceChauffage::pch()
     */
    public function pch(): float
    {
        return $this->generateur->data()->pch;
    }

    /**
     * Puissance de dimensionnement du générateur exprimée en kW
     */
    public function pdim(): float
    {
        return ($pecs = $this->pecs()) ? max($pecs, $this->pecs()) : $this->pch();
    }

    /**
     * Puissance nominale conventionnelle exprimée en kW
     */
    public function pn(): ?float
    {
        return $this->get("pn", function () {
            if (null !== $this->generateur->signaletique()->pn) {
                return $this->generateur->signaletique()->pn;
            }
            if (false === $this->generateur->type()->is_chaudiere()) {
                return $this->pch();
            }
            if (null === $pn = $this->table_repository->pn(
                type_chaudiere: $this->generateur->signaletique()->type_chaudiere ?? TypeChaudiere::CHAUDIERE_SOL,
                annee_installation_generateur: $this->generateur->annee_installation() ?? $this->audit->batiment()->annee_construction,
                pdim: $this->pdim(),
            )) {
                throw new \DomainException('Valeur forfaitaire Pn non trouvée');
            }
            return $pn;
        });
    }

    public function apply(Audit $entity): void
    {
        foreach ($entity->chauffage()->generateurs() as $generateur) {
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
            PuissanceChauffage::class,
            PuissanceEcs::class,
        ];
    }
}
