<?php

namespace App\Domain\Baie\Table;

use App\Domain\Common\Table\InterpolationSimple;
use App\Domain\Common\Table\TableValueCollection;

/**
 * @property Uw[] $elements
 */
class UwCollection extends TableValueCollection
{
    use InterpolationSimple;

    public function uw(float $ug): ?float
    {
        $x = $ug;
        return $this->p($x);
    }

    /**
     * @inheritdoc
     */
    public function sequence(float $x): array
    {
        $xs = $this
            ->usort(fn (Uw $a, Uw $b): int => \round(\abs($a->x() - $x) - \abs($b->x() - $x)))
            ->slice(0, 2);

        return [
            'x' => $x,
            'x1' => \reset($xs->elements)?->x(),
            'x2' => \end($xs->elements)?->x(),
            'y1' => \reset($xs->elements)?->y(),
            'y2' => \end($xs->elements)?->y(),
        ];
    }
}
