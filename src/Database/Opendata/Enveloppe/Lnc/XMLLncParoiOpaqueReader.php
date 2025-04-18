<?php

namespace App\Database\Opendata\Enveloppe\Lnc;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Enveloppe\Enum\{EtatIsolation, Mitoyennete};

interface XMLLncParoiOpaqueReader
{
    public function id(): Id;

    public function description(): string;

    public function mitoyennete(): Mitoyennete;

    public function isolation(): ?EtatIsolation;

    public function surface(): float;
}
