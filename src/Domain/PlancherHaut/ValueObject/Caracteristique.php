<?php

namespace App\Domain\PlancherHaut\ValueObject;

use App\Domain\PlancherHaut\Enum\{Inertie, TypePlancherHaut};

/**
 * Caractéristiques d'un plancher haut
 */
final class Caracteristique
{
    public function __construct(
        public readonly TypePlancherHaut $type_plancher_haut,
        public readonly Inertie $inertie,
        public readonly Surface $surface,
        public readonly ?Uph0 $uph0,
        public readonly ?Uph $uph,
    ) {
    }
}
