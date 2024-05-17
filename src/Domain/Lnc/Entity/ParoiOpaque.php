<?php

namespace App\Domain\Lnc\Entity;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Lnc\Lnc;
use App\Domain\Lnc\ValueObject\SurfaceParoi;

/**
 * Paroi opaque du local non chauffé donnant sur l'extérieur ou en contact avec le sol (paroi enterrée, terre-plein)
 */
final class ParoiOpaque implements Paroi
{
    public function __construct(
        private readonly Id $id,
        private readonly Lnc $local_non_chauffe,
        private string $description,
        private SurfaceParoi $surface,
        private bool $isolation,
    ) {
    }

    public static function create(
        Lnc $local_non_chauffe,
        string $description,
        SurfaceParoi $surface,
        bool $isolation,
    ): self {
        return new self(
            id: Id::create(),
            local_non_chauffe: $local_non_chauffe,
            description: $description,
            surface: $surface,
            isolation: $isolation,
        );
    }

    public function update(string $description, SurfaceParoi $surface, bool $isolation): self
    {
        $this->description = $description;
        $this->surface = $surface;
        $this->isolation = $isolation;
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

    public function surface(): SurfaceParoi
    {
        return $this->surface;
    }

    public function isolation(): bool
    {
        return $this->isolation;
    }
}
