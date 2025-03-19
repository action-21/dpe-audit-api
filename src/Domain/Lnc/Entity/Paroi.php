<?php

namespace App\Domain\Lnc\Entity;

use App\Domain\Lnc\ValueObject\SurfaceDeperditiveParoi;

abstract class Paroi
{
    protected ?SurfaceDeperditiveParoi $surface_deperditive = null;

    public function surface_deperditive(): ?SurfaceDeperditiveParoi
    {
        return $this->surface_deperditive;
    }
}
