<?php

namespace App\Engine\Performance\Chauffage\Dimensionnement;

use App\Domain\Audit\Audit;
use App\Domain\Chauffage\Entity\{Generateur, Systeme};
use App\Engine\Performance\Deperdition\DeperditionEnveloppe;
use App\Engine\Performance\Rule;
use App\Engine\Performance\Scenario\ScenarioClimatique;

final class PuissanceChauffage extends Rule
{
    private Audit $audit;
    private Generateur $generateur;

    /**
     * @see \App\Engine\Performance\Scenario\ScenarioClimatique::tbase()
     */
    public function tbase(): float
    {
        return $this->audit->data()->tbase;
    }

    /**
     * TODO
     */
    public function ratio_proratisation(): float
    {
        return 1;
    }

    /**
     * @see \App\Engine\Performance\Deperdition\DeperditionEnveloppe::gv()
     */
    public function gv(): float
    {
        return $this->audit->enveloppe()->data()->deperditions->get();
    }

    /**
     * @see \App\Engine\Performance\Chauffage\Dimensionnement\DimensionnementSysteme::rdim()
     * @see \App\Engine\Performance\Chauffage\Dimensionnement\DimensionnementInstallation::rdim()
     */
    public function rdim(): float
    {
        return $this->generateur->systemes()->reduce(
            fn(float $rdim, Systeme $item) => $rdim + $item->data()->rdim * $item->installation()->data()->rdim
        );
    }

    /**
     * Puissance conventionnelle de chauffage exprimée en W
     */
    public function pch(): float
    {
        $tbase = $this->tbase();
        $gv = $this->gv();
        $rdim = $this->rdim();
        $pch = (1.2 * $gv * (19 - $tbase)) / (1000 * \pow(0.95, 3)) * 1000 * $rdim;

        return $this->generateur->position()->generateur_collectif
            ? $pch * (1 / $this->ratio_proratisation())
            : $pch;
    }

    public function apply(Audit $entity): void
    {
        $this->audit = $entity;

        foreach ($entity->chauffage()->generateurs() as $generateur) {
            $this->generateur = $generateur;

            $generateur->calcule($generateur->data()->with(
                pch: $this->pch(),
            ));
        }
    }

    public static function dependencies(): array
    {
        return [
            ScenarioClimatique::class,
            DeperditionEnveloppe::class,
            DimensionnementSysteme::class,
            DimensionnementInstallation::class,
        ];
    }
}
