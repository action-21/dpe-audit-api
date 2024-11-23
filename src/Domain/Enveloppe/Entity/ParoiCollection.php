<?php

namespace App\Domain\Enveloppe\Entity;

use App\Domain\Common\Type\Id;

interface ParoiCollection
{
    public function reinitialise(): self;
    public function find(Id $id): ?Paroi;
    public function filter_by_local_non_chauffe(Id $id): self;
    public function filter_by_isolation(bool $isolation): self;
    public function surface(): float;
    public function surface_deperditive(): float;
}
