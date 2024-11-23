<?php

namespace App\Domain\Enveloppe\Entity;

use App\Domain\Common\Enum\Enum;
use App\Domain\Common\Type\Id;
use App\Domain\Lnc\Lnc;

interface Paroi
{
    public function id(): Id;
    public function local_non_chauffe(): ?Lnc;
    public function mitoyennete(): Enum;
    public function orientation(): ?float;
    public function b(): ?float;
}
