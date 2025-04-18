<?php

namespace App\Database\Opendata\Enveloppe\Lnc;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Enveloppe\Enum\EtatIsolation;
use App\Domain\Enveloppe\Enum\Mitoyennete;

final class XMLLncVirtuelParoiReader implements XMLLncParoiOpaqueReader
{
    public function __construct(
        public readonly float $surface,
        public readonly Mitoyennete $mitoyennete,
        public readonly ?EtatIsolation $isolation,
    ) {}

    public function id(): Id
    {
        return Id::create();
    }

    public function description(): string
    {
        return 'Paroi opaque reconstituée';
    }

    public function surface(): float
    {
        return $this->surface;
    }

    public function mitoyennete(): Mitoyennete
    {
        return $this->mitoyennete;
    }

    public function isolation(): ?EtatIsolation
    {
        return $this->isolation;
    }
}
