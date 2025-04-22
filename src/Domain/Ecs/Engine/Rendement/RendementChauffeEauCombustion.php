<?php

namespace App\Domain\Ecs\Engine\Rendement;

use App\Domain\Common\Enum\ScenarioUsage;
use App\Domain\Ecs\Entity\Systeme;
use App\Domain\Ecs\Enum\{EnergieGenerateur, TypeGenerateur};

final class RendementChauffeEauCombustion extends RendementSysteme
{
    /**
     * @see \App\Domain\Ecs\Engine\Performance\PerformanceGenerateurCombustion::rpn()
     */
    public function rpn(): float
    {
        return $this->generateur()->data()->rpn->decimal();
    }

    /**
     * @see \App\Domain\Ecs\Engine\Performance\PerformanceGenerateurCombustion::qp0()
     */
    public function qp0(): float
    {
        return $this->generateur()->data()->qp0;
    }

    /**
     * @see \App\Domain\Ecs\Engine\Performance\PerformanceGenerateurCombustion::pveilleuse()
     */
    public function pveilleuse(): float
    {
        return $this->generateur()->data()->pveilleuse;
    }

    public function rgs(ScenarioUsage $scenario): float
    {
        $becs = $this->becs($scenario);
        $rpn = $this->rpn();
        $qp0 = $this->qp0();
        $pveilleuse = $this->pveilleuse();
        $pertes = $this->pertes_stockage($scenario);

        return $this->generateur()->type() === TypeGenerateur::ACCUMULATEUR
            ? 1 / ((1 / $rpn) + ((8592 * $qp0 + $pertes) / $becs) + (6970 * ($pveilleuse / $becs)))
            : 1 / ((1 / $rpn) + ((1790 * $qp0 + $pertes) / $becs) + (6970 * ((0.5 * $pveilleuse) / $becs)));
    }

    public function rg(ScenarioUsage $scenario): float
    {
        return $this->get("rg", function () {
            if (null === $rg = $this->table_repository->rg(
                type_generateur: $this->generateur()->type(),
                energie_generateur: $this->generateur()->energie(),
            )) {
                throw new \RuntimeException('Valeur forfaitaire Rg non trouvée');
            }
            return $rg;
        });
    }

    public static function supports(Systeme $systeme): bool
    {
        return in_array($systeme->generateur()->type(), [
            TypeGenerateur::ACCUMULATEUR,
            TypeGenerateur::CHAUDIERE,
            TypeGenerateur::POELE_BOUILLEUR,
        ]) && $systeme->generateur()->energie() !== EnergieGenerateur::ELECTRICITE;
    }
}
