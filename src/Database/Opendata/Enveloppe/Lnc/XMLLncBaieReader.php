<?php

namespace App\Database\Opendata\Enveloppe\Lnc;

use App\Domain\Common\ValueObject\{Id, Inclinaison, Orientation};
use App\Domain\Enveloppe\Enum\Lnc\{Materiau, TypeBaie, TypeVitrage};
use App\Domain\Enveloppe\Enum\Mitoyennete;

interface XMLLncBaieReader
{
    public function id(): Id;

    public function description(): string;

    public function type(): TypeBaie;

    public function materiau(): ?Materiau;

    public function type_vitrage(): ?TypeVitrage;

    public function presence_rupteur_pont_thermique(): ?bool;

    public function mitoyennete(): Mitoyennete;

    public function surface(): float;

    public function inclinaison(): Inclinaison;

    public function orientation(): ?Orientation;

    public function paroi_id(): ?Id;
}
