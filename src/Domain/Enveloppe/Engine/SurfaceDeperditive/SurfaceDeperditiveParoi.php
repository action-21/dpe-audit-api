<?php

namespace App\Domain\Enveloppe\Engine\SurfaceDeperditive;

use App\Domain\Common\EngineRule;
use App\Domain\Enveloppe\Entity\Paroi;
use App\Domain\Enveloppe\Enum\{EtatIsolation, Mitoyennete};

abstract class SurfaceDeperditiveParoi extends EngineRule
{
    protected Paroi $paroi;

    abstract public function isolation(): EtatIsolation;

    /**
     * Surface déperditive exprimée en m²
     */
    public function sdep(): float
    {
        return $this->paroi->mitoyennete() !== Mitoyennete::LOCAL_RESIDENTIEL
            ? $this->paroi->surface_reference()
            : 0;
    }
}
