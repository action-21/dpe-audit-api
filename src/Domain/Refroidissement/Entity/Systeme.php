<?php

namespace App\Domain\Refroidissement\Entity;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Refroidissement\Data\SystemeData;
use App\Domain\Refroidissement\Refroidissement;

final class Systeme
{
    public function __construct(
        private readonly Id $id,
        private readonly Refroidissement $refroidissement,
        private readonly Installation $installation,
        private readonly Generateur $generateur,
        private SystemeData $data,
    ) {}

    public static function create(
        Id $id,
        Refroidissement $refroidissement,
        Installation $installation,
        Generateur $generateur,
    ): self {
        return new self(
            id: $id,
            refroidissement: $refroidissement,
            installation: $installation,
            generateur: $generateur,
            data: SystemeData::create(),
        );
    }

    public function reinitialise(): self
    {
        $this->data = SystemeData::create();
        return $this;
    }

    public function calcule(SystemeData $data): self
    {
        $this->data = $data;
        return $this;
    }

    public function id(): Id
    {
        return $this->id;
    }

    public function refroidissement(): Refroidissement
    {
        return $this->refroidissement;
    }

    public function installation(): Installation
    {
        return $this->installation;
    }

    public function generateur(): Generateur
    {
        return $this->generateur;
    }

    public function data(): SystemeData
    {
        return $this->data;
    }
}
