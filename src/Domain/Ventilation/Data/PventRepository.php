<?php

namespace App\Domain\Ventilation\Data;

use App\Domain\Ventilation\Enum\{ModeExtraction, TypeSysteme};

interface PventRepository
{
    public function find_by(
        TypeSysteme $type_systeme,
        ?ModeExtraction $mode_extraction,
        ?int $annee_installation,
        ?bool $systeme_collectif,
    ): ?Pvent;
}
