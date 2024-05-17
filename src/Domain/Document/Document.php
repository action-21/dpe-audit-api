<?php

namespace App\Domain\Document;

use App\Domain\Audit\Audit;
use App\Domain\Common\ValueObject\Id;

/**
 * Document justificatif utilisée pour l'établissement de l'audit
 */
final class Document
{
    public function __construct(
        private readonly \Stringable $id,
        private readonly Audit $audit,
        private readonly \DateTimeImmutable $date_creation,
        private \DateTimeImmutable $date_modification,
        private string $nom,
        private string $description,
    ) {
    }

    public static function create(Audit $audit, string $nom, string $description): self
    {
        return new self(
            id: Id::create(),
            audit: $audit,
            date_creation: new \DateTimeImmutable(),
            date_modification: new \DateTimeImmutable(),
            nom: $nom,
            description: $description,
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

    public function nom(): string
    {
        return $this->nom;
    }

    public function description(): string
    {
        return $this->description;
    }
}
