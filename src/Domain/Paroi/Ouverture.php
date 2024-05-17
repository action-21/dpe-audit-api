<?php

namespace App\Domain\Paroi;

use App\Domain\Common\Enum\Enum;

interface Ouverture extends Paroi
{
    /**
     * Paroi opaque associée à l'ouverture
     */
    public function paroi_opaque(): ?ParoiOpaque;

    /**
     * Présence de joint d'étanchéité
     */
    public function presence_joint(): bool;

    /**
     * Présence d'un retour d'isolation
     */
    public function presence_retour_isolation(): ?bool;

    /**
     * Largeur du dormant en mm
     */
    public function largeur_dormant(): ?float;

    /**
     * Type de pose de l'ouverture
     */
    public function type_pose(): Enum;

    /**
     * Surface totale de l'ouverture (vitrage + menuiserie) en m²
     */
    public function surface(): float;
}
