<?php

namespace App\Domain\Enveloppe\Entity;

use App\Domain\Audit\{Audit, AuditTrait};
use App\Domain\Common\Enum\Enum;
use App\Domain\Common\ValueObject\Id;
use App\Domain\Enveloppe\Enveloppe;
use App\Domain\Lnc\Lnc;

abstract class Paroi
{
    use AuditTrait;

    public readonly Enveloppe $enveloppe;
    public readonly Id $id;

    public function audit(): Audit
    {
        return $this->enveloppe->audit();
    }

    abstract public function local_non_chauffe(): ?Lnc;
    abstract public function mitoyennete(): Enum;
    abstract public function orientation(): ?float;
    abstract public function b(): ?float;
}
