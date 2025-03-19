<?php

namespace App\Domain\Lnc\Entity;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Lnc\Enum\EtatIsolation;
use App\Domain\Lnc\Lnc;
use App\Domain\Lnc\Service\MoteurSurfaceDeperditiveParoiOpaque;
use App\Domain\Lnc\ValueObject\PositionParoi;
use Webmozart\Assert\Assert;

final class ParoiOpaque extends Paroi
{
    public function __construct(
        private readonly Id $id,
        private readonly Lnc $local_non_chauffe,
        private string $description,
        private ?EtatIsolation $isolation,
        private PositionParoi $position,
    ) {}

    public function controle(): void
    {
        $this->position->controle();
        Assert::greaterThanEq($this->surface_opaque(), 0);
    }

    public function reinitialise(): void
    {
        $this->surface_deperditive = null;
    }

    public function calcule_surface_deperditive(MoteurSurfaceDeperditiveParoiOpaque $moteur): self
    {
        $this->surface_deperditive = $moteur($this);
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

    public function position(): PositionParoi
    {
        return $this->position;
    }

    public function surface_opaque(): float
    {
        return $this->position->surface - $this->local_non_chauffe->baies()->filter_by_paroi(id: $this->id)->surface();
    }
}
