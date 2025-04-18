<?php

namespace App\Database\Opendata\Enveloppe\Lnc;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Enveloppe\Enum\Lnc\{TypeLnc};

interface XMLLncReader
{
    public function id(): Id;

    public function description(): string;

    public function type(): TypeLnc;

    /**
     * @return XMLLncParoiOpaqueReader[]
     */
    public function parois_opaques(): array;

    /**
     * @return XMLLncBaieReader[]
     */
    public function baies(): array;
}
