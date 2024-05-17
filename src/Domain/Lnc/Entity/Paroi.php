<?php

namespace App\Domain\Lnc\Entity;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Lnc\Lnc;
use App\Domain\Lnc\ValueObject\SurfaceParoi;

/**
 * Paroi du local non chauffé donnant sur l'extérieur ou en contact avec le sol (paroi enterrée, terre-plein)
 */
interface Paroi
{
    /**
     * Identifiant unique de la proi
     */
    public function id(): Id;

    /**
     * Local non chauffé
     */
    public function local_non_chauffe(): Lnc;

    /**
     * Description de la paroi
     */
    public function description(): string;

    /**
     * Surface de la paroi
     */
    public function surface(): SurfaceParoi;

    /**
     * État d'isolation de la paroi
     */
    public function isolation(): bool;
}
