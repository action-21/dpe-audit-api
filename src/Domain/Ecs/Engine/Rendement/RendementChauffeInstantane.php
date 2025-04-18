<?php

namespace App\Domain\Ecs\Engine\Rendement;

use App\Domain\Common\Enum\ScenarioUsage;
use App\Domain\Ecs\Entity\Systeme;
use App\Domain\Ecs\Enum\{EnergieGenerateur, TypeGenerateur};

final class RendementChauffeInstantane extends RendementSysteme
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

    public function rg(ScenarioUsage $scenario): float
    {
        $becs = $this->becs($scenario);
        $rpn = $this->rpn();
        $qp0 = $this->qp0();
        $pveilleuse = $this->pveilleuse();

        return 1 / ((1 / $rpn) + (1790 * ($qp0 / $becs)) + (6970 * ($pveilleuse / $becs)));
    }

    public static function supports(Systeme $systeme): bool
    {
        return $systeme->generateur()->type() === TypeGenerateur::CHAUFFE_EAU_INSTANTANE
            && $systeme->generateur()->energie() !== EnergieGenerateur::ELECTRICITE;
    }
}
