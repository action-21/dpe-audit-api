<?php

namespace App\Domain\Paroi;

use App\Domain\Common\Enum\Enum;
use App\Domain\Common\ValueObject\Id;
use App\Domain\Enveloppe\Enveloppe;
use App\Domain\Lnc\Lnc;

interface Paroi
{
    /**
     * Identifiant unique de la paroi
     */
    public function id(): Id;

    /**
     * Bâtiment rattaché à la paroi
     */
    public function enveloppe(): Enveloppe;

    /**
     * Local non chauffé associé à la paroi
     */
    public function local_non_chauffe(): ?Lnc;

    /**
     * Type de paroi
     */
    public function type_paroi(): TypeParoi;

    /**
     * Surface déperditive de la paroi en m²
     */
    public function surface_deperditive(): float;

    /**
     * État d'isolation de la paroi
     */
    public function est_isole(): bool;

    /**
     * Mitoyenneté de la paroi
     */
    public function mitoyennete(): Enum;
}
