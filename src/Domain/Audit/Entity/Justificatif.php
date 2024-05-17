<?php

namespace App\Domain\Audit\Entity;

use App\Domain\Audit\Audit;
use App\Domain\Common\ValueObject\Id;
use App\Domain\Justificatif\Enum\TypeJustificatif;

/**
 * Justificatif de saisie
 */
final class Justificatif
{
    public function __construct(
        private readonly Id $id,
        private readonly Audit $audit,
        private readonly \DateTimeImmutable $date_creation,
        private \DateTimeImmutable $date_modification,
        private TypeJustificatif $type,
    ) {
    }

    public static function create(Audit $audit, TypeJustificatif $type): self
    {
        return new self(
            id: Id::create(),
            audit: $audit,
            date_creation: new \DateTimeImmutable(),
            date_modification: new \DateTimeImmutable(),
            type: $type,
        );
    }

    public function id(): Id
    {
        return $this->id;
    }

    public function audit(): Audit
    {
        return $this->audit;
    }

    public function date_creation(): \DateTimeImmutable
    {
        return $this->date_creation;
    }

    public function date_modification(): \DateTimeImmutable
    {
        return $this->date_modification;
    }

    public function type(): TypeJustificatif
    {
        return $this->type;
    }
}
