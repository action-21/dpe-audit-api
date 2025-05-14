<?php

namespace App\Engine\Performance\SurfaceDeperditive;

use App\Domain\Enveloppe\Entity\Paroi;
use App\Domain\Enveloppe\Enum\{EtatIsolation, Mitoyennete};
use App\Engine\Performance\Rule;

/**
 * @template T
 * 
 * @property Paroi|T $paroi
 */
abstract class SurfaceDeperditiveParoi extends Rule
{
    protected Paroi $paroi;

    /**
     * Etat d'isolation de la paroi
     */
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
