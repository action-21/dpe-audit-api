<?php

namespace App\Domain\PlancherBas\Table;

use App\Domain\Common\Table\TableValueCollection;
use App\Domain\Common\Table\InterpolationDouble;

/**
 * @property Ue[] $elements
 */
final class UeCollection extends TableValueCollection
{
    use InterpolationDouble;

    public function ue(float $upb, float $surface, float $perimetre): float
    {
        $x = $upb;
        $y = $perimetre ? 2 * $surface / $perimetre : 0;

        return $this->p($x, $y);
    }

    /**
     * @inheritdoc
     * 
     * @param float $x = Upb
     * @param float $y = 2S/P
     * @return float[]
     */
    public function sequence(float $x, float $y): array
    {
        /** @var float[] */
        $sequence = [];

        $xs = $this->xs($x);
        $ys = $this->filter(fn (Ue $item): bool => \in_array($item->x(), $xs))->ys($y);

        $sequence['x1'] = $xs[0] ?? null;
        $sequence['x2'] = $xs[1] ?? null;
        $sequence['y1'] = $ys[0] ?? null;
        $sequence['y2'] = $ys[1] ?? null;
        $sequence['q11'] = $this->q($sequence['x1'], $sequence['y1']);
        $sequence['q12'] = $this->q($sequence['x1'], $sequence['y2']);
        $sequence['q21'] = $this->q($sequence['x2'], $sequence['y1']);
        $sequence['q22'] = $this->q($sequence['x2'], $sequence['y2']);

        return $sequence;
    }

    /**
     * Retourne les deux valeurs tabulaires de x les plus proches de $x
     * 
     * @param float $x = upb
     * @return float[]
     */
    public function xs(float $x): array
    {
        $xs = $this
            ->usort(fn (Ue $a, Ue $b): int => \round(\abs($a->x() - $x) - \abs($b->x() - $x)))
            ->map(fn (Ue $item): float => $item->x());
        return \array_slice([...\array_unique($xs)], 0, 2);
    }

    /**
     * Retourne les deux valeurs tabulaires de y les plus proches de $y
     * 
     * @param float $y = 2S/P
     * @return float[]
     */
    public function ys(float $y): array
    {
        $ys = $this
            ->usort(fn (Ue $a, Ue $b): int => \round(\abs($a->y() - $y) - \abs($b->y() - $y)))
            ->map(fn (Ue $item): float => $item->y());
        return \array_slice([...\array_unique($ys)], 0, 2);
    }

    /**
     * @param ?float $x = Upb
     * @param ?float $y = 2S/P
     * @return ?float = Ue
     */
    public function q(?float $x, ?float $y): ?float
    {
        foreach ($this->elements as $value) {
            if ($value->x() === $x && $value->y() == $y) {
                return $value->valeur();
            }
        }
        return null;
    }
}
