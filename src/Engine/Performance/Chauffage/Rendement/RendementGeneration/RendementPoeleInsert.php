<?php

namespace App\Engine\Performance\Chauffage\Rendement\RendementGeneration;

use App\Domain\Chauffage\Entity\Systeme;
use App\Domain\Common\Enum\ScenarioUsage;
use App\Domain\Common\ValueObject\Annee;
use App\Engine\Performance\Chauffage\Rendement\RendementGeneration;

final class RendementPoeleInsert extends RendementGeneration
{
    public function annee_installation(): Annee
    {
        return $this->generateur()->annee_installation() ?? $this->audit->batiment()->annee_construction;
    }

    public function rg(ScenarioUsage $scenario): float
    {
        if (null === $rg = $this->table_repository->rg(
            type_generateur: $this->generateur()->type(),
            energie_generateur: $this->generateur()->energie(),
            label_generateur: $this->generateur()->signaletique()->label,
            anne_installation_generateur: $this->annee_installation(),
        )) {
            throw new \DomainException('Valeur forfaitaire Rg non trouvée');
        }
        return $rg;
    }

    public static function match(Systeme $systeme): bool
    {
        return $systeme->generateur()->type()->is_poele_insert()
            && $systeme->generateur()->energie()->is_combustible()
            && false === $systeme->generateur()->position()->generateur_multi_batiment;
    }
}
