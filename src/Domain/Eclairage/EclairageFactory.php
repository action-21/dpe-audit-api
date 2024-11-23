<?php

namespace App\Domain\Eclairage;

use App\Domain\Audit\Audit;

final class EclairageFactory
{
    public function build(Audit $audit): Eclairage
    {
        return new Eclairage($audit);
    }
}
