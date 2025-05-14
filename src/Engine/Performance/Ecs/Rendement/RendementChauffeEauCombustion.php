<?php

namespace App\Engine\Performance\Ecs\Rendement;

use App\Domain\Common\Enum\ScenarioUsage;
use App\Domain\Ecs\Entity\Systeme;
use App\Domain\Ecs\Enum\{EnergieGenerateur, TypeGenerateur};

final class RendementChauffeEauCombustion extends RendementSysteme
{
    /**
     * @see \App\Engine\Performance\Ecs\Performance\PerformanceGenerateurCombustion::rpn()
     */
    public function rpn(): float
    {
        return $this->generateur()->data()->rpn->decimal();
    }

    /**
     * @see \App\Engine\Performance\Ecs\Performance\PerformanceGenerateurCombustion::qp0()
     */
    public function qp0(): float
    {
        return $this->generateur()->data()->qp0;
    }

    /**
     * @see \App\Engine\Performance\Ecs\Performance\PerformanceGenerateurCombustion::pveilleuse()
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

    public static function match(Systeme $systeme): bool
    {
        return in_array($systeme->generateur()->type(), [
            TypeGenerateur::ACCUMULATEUR,
            TypeGenerateur::CHAUDIERE,
            TypeGenerateur::POELE_BOUILLEUR,
        ]) && $systeme->generateur()->energie() !== EnergieGenerateur::ELECTRICITE;
    }
}
