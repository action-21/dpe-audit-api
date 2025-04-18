<?php

namespace App\Domain\Ecs\Entity;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Ecs\Data\SystemeData;
use App\Domain\Ecs\Ecs;
use App\Domain\Ecs\ValueObject\{Reseau, Stockage};

final class Systeme
{
    public function __construct(
        private readonly Id $id,
        private readonly Ecs $ecs,
        private readonly Installation $installation,
        private readonly Generateur $generateur,
        private Reseau $reseau,
        private ?Stockage $stockage,
        private SystemeData $data,
    ) {}

    public static function create(
        Id $id,
        Ecs $ecs,
        Installation $installation,
        Generateur $generateur,
        Reseau $reseau,
        ?Stockage $stockage,
    ): self {
        return new self(
            id: $id,
            ecs: $ecs,
            installation: $installation,
            generateur: $generateur,
            reseau: $reseau,
            stockage: $stockage,
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

    public function ecs(): Ecs
    {
        return $this->ecs;
    }

    public function installation(): Installation
    {
        return $this->installation;
    }

    public function generateur(): Generateur
    {
        return $this->generateur;
    }

    public function reseau(): Reseau
    {
        return $this->reseau;
    }

    public function stockage(): ?Stockage
    {
        return $this->stockage;
    }

    public function data(): SystemeData
    {
        return $this->data;
    }
}
