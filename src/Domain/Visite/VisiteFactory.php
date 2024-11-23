<?php

namespace App\Domain\Visite;

use App\Domain\Audit\Audit;
use App\Domain\Visite\Entity\LogementCollection;

final class VisiteFactory
{
    public function build(Audit $audit): Visite
    {
        return new Visite(
            audit: $audit,
            logements: new LogementCollection(),
        );
    }
}
