<?php

namespace App\Domain\Enveloppe\Engine\Inertie;

use App\Domain\Audit\Audit;
use App\Domain\Common\EngineRule;
use App\Domain\Enveloppe\Entity\Niveau;
use App\Domain\Enveloppe\Enum\Inertie;

final class InertieNiveau extends EngineRule
{
    private Niveau $niveau;

    /**
     * Etat d'inertie du niveau
     */
    public function inertie(): Inertie
    {
        $inerties = array_filter([
            $this->niveau->inertie_paroi_verticale(),
            $this->niveau->inertie_plancher_bas(),
            $this->niveau->inertie_plancher_haut(),
        ], fn(Inertie $inertie) => $inertie !== Inertie::LOURDE && $inertie !== Inertie::TRES_LOURDE);

        return match (true) {
            count($inerties) === 0 => Inertie::LEGERE,
            count($inerties) === 1 => Inertie::MOYENNE,
            count($inerties) === 2 => Inertie::LOURDE,
            count($inerties) === 3 => Inertie::TRES_LOURDE,
        };
    }

    public function apply(Audit $entity): void
    {
        foreach ($entity->enveloppe()->niveaux() as $niveau) {
            $this->niveau = $niveau;
            $niveau->calcule($niveau->data()->with(
                inertie: $this->inertie(),
            ));
        }
    }
}
