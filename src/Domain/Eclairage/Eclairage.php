<?php

namespace App\Domain\Eclairage;

use App\Domain\Common\ValueObject\Id;

final class Eclairage
{
    public function __construct(
        private readonly Id $id,
        private EclairageData $data,
    ) {}

    public static function create(): self
    {
        return new self(
            id: Id::create(),
            data: EclairageData::create(),
        );
    }

    public function reinitialise(): void
    {
        $this->data = EclairageData::create();
    }

    public function calcule(EclairageData $data): self
    {
        $this->data = $data;
        return $this;
    }

    public function id(): Id
    {
        return $this->id;
    }

    public function data(): EclairageData
    {
        return $this->data;
    }
}
