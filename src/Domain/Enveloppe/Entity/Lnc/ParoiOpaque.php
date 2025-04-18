<?php

namespace App\Domain\Enveloppe\Entity\Lnc;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Enveloppe\Data\Lnc\ParoiOpaqueData;
use App\Domain\Enveloppe\Enum\EtatIsolation;
use App\Domain\Enveloppe\Entity\Lnc;
use App\Domain\Enveloppe\ValueObject\Lnc\PositionParoi as Position;

final class ParoiOpaque
{
    public function __construct(
        private readonly Id $id,
        private readonly Lnc $local_non_chauffe,
        private string $description,
        private ?EtatIsolation $isolation,
        private Position $position,
        private ParoiOpaqueData $data,
    ) {}

    public static function create(
        Id $id,
        Lnc $local_non_chauffe,
        string $description,
        ?EtatIsolation $isolation,
        Position $position,
    ): self {
        return new self(
            id: $id,
            local_non_chauffe: $local_non_chauffe,
            description: $description,
            isolation: $isolation,
            position: $position,
            data: ParoiOpaqueData::create(),
        );
    }

    public function reinitialise(): self
    {
        $this->data = ParoiOpaqueData::create();
        return $this;
    }

    public function calcule(ParoiOpaqueData $data): self
    {
        $this->data = $data;
        return $this;
    }

    public function id(): Id
    {
        return $this->id;
    }

    public function local_non_chauffe(): Lnc
    {
        return $this->local_non_chauffe;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function isolation(): ?EtatIsolation
    {
        return $this->isolation;
    }

    public function position(): Position
    {
        return $this->position;
    }

    public function surface_opaque(): float
    {
        return $this->position->surface
            - $this->local_non_chauffe->baies()->with_paroi(id: $this->id)->surface();
    }

    public function data(): ParoiOpaqueData
    {
        return $this->data;
    }
}
