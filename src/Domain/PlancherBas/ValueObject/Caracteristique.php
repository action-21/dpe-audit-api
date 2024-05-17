<?php

namespace App\Domain\PlancherBas\ValueObject;

use App\Domain\PlancherBas\Enum\{Inertie, TypePlancherBas};

/**
 * Caractéristiques d'un plancher bas
 */
final class Caracteristique
{
    public function __construct(
        public readonly TypePlancherBas $type_plancher_bas,
        public readonly Inertie $inertie,
        public readonly Perimetre $perimetre,
        public readonly Surface $surface,
        public readonly ?Upb0 $upb0,
        public readonly ?Upb $upb,
    ) {
    }
}
