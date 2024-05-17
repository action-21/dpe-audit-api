<?php

namespace App\Database\Opendata\Lnc;

use App\Database\Opendata\{XMLReaderIterator};
use App\Domain\Common\Identifier\Uuid;
use App\Domain\Lnc\ValueObject\SurfaceParoi;

final class XMLLncParoiReader extends XMLReaderIterator
{
    private XMLLncReader $reader;

    public function id(): \Stringable
    {
        return Uuid::create();
    }

    public function description(): string
    {
        return 'Paroi non décrite';
    }

    public function surface(): SurfaceParoi
    {
        return SurfaceParoi::from($this->reader->surface_aue());
    }

    public function isolation(): bool
    {
        return $this->reader->enum_isolation_lnc()->isolation_aue();
    }

    public function read(XMLLncReader $reader): self
    {
        $this->array = [$reader->get()];
        $this->reader = $reader;
        return $this;
    }
}
