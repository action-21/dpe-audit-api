<?php

namespace App\Engine\Performance\Ecs\Dimensionnement;

use App\Domain\Audit\Audit;
use App\Domain\Ecs\Entity\{Generateur, Systeme};
use App\Engine\Performance\Rule;

final class PuissanceEcs extends Rule
{
    private Generateur $generateur;

    /**
     * Puissance de dimensionnement du besoin d'eau chaude sanitaire en kW
     */
    public function pecs(): float
    {
        if ($this->generateur->signaletique()->pn) {
            return $this->generateur->signaletique()->pn;
        }
        $volume_stockage = $this->generateur->signaletique()->volume_stockage;
        $volume_stockage += $this->generateur->systemes()->reduce(
            fn(float $vs, Systeme $systeme) => $vs + ($systeme->stockage()?->volume_stockage ?? 0),
        );

        return match (true) {
            $volume_stockage === 0 => 21,
            $volume_stockage <= 20 => 21 - 0.8 * $volume_stockage,
            $volume_stockage <= 150 => 5 - 1.751 * (($volume_stockage - 20) / 65),
            $volume_stockage > 150 => (7.14 * $volume_stockage + 428) / 1000,
        };
    }

    public function apply(Audit $entity): void
    {
        foreach ($entity->ecs()->generateurs() as $generateur) {
            $this->generateur = $generateur;
            $generateur->calcule($generateur->data()->with(
                pecs: $this->pecs(),
            ));
        }
    }
}
