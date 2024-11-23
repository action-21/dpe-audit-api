<?php

namespace App\Domain\Ventilation\Data;

use App\Domain\Ventilation\Enum\{ModeExtraction, ModeInsufflation, TypeSysteme};

interface DebitRepository
{
    public function find_by(
        TypeSysteme $type_systeme,
        ?ModeExtraction $mode_extraction,
        ?ModeInsufflation $mode_insufflation,
        ?bool $presence_echangeur,
        ?bool $systeme_collectif,
        ?int $annee_installation,
    ): ?Debit;
}
