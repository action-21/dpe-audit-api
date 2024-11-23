<?php

namespace App\Domain\Ventilation\Factory;

use App\Domain\Common\Type\Id;
use App\Domain\Ventilation\Entity\{Installation, SystemeCollection};
use App\Domain\Ventilation\Ventilation;

final class InstallationFactory
{
    public function build(
        Id $id,
        Ventilation $ventilation,
        float $surface,
    ): Installation {
        return new Installation(
            id: $id,
            ventilation: $ventilation,
            surface: $surface,
            systemes: new SystemeCollection(),
        );
    }
}
