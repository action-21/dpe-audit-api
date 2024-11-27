<?php

namespace App\Domain\Common\Service;

final class Interpolation
{
    public function interpolation_lineaire(float $x, float $x1, float $x2, float $y1, float $y2): float
    {
        return $y1 + ($x - $x1) * (($y2 - $y1) / \max($x2 - $x1, 1));
    }
}
