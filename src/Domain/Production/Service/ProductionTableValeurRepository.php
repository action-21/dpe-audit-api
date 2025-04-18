<?php

namespace App\Domain\Production\Service;

use App\Domain\Common\ValueObject\{Inclinaison, Orientation};

interface ProductionTableValeurRepository
{
    public function kpv(Orientation $orientation, Inclinaison $inclinaison): ?float;
}
