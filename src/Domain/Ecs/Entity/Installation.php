<?php

namespace App\Domain\Ecs\Entity;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Ecs\Data\InstallationData;
use App\Domain\Ecs\Ecs;
use App\Domain\Ecs\ValueObject\Solaire;
use Webmozart\Assert\Assert;

final class Installation
{
    public function __construct(
        private readonly Id $id,
        private readonly Ecs $ecs,
        private string $description,
        private float $surface,
        private ?Solaire $solaire_thermique,
        private InstallationData $data,
    ) {}

    public static function create(
        Id $id,
        Ecs $ecs,
        string $description,
        float $surface,
        ?Solaire $solaire_thermique,
    ): self {
        Assert::greaterThan($surface, 0);

        return new self(
            id: $id,
            ecs: $ecs,
            description: $description,
            surface: $surface,
            solaire_thermique: $solaire_thermique,
            data: InstallationData::create(),
        );
    }

    public function reinitialise(): self
    {
        $this->data = InstallationData::create();
        return $this;
    }

    public function calcule(InstallationData $data): self
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

    public function description(): string
    {
        return $this->description;
    }

    public function surface(): float
    {
        return $this->surface;
    }

    public function solaire_thermique(): ?Solaire
    {
        return $this->solaire_thermique;
    }

    /**
     * @return SystemeCollection|Systeme[]
     */
    public function systemes(): SystemeCollection
    {
        return $this->ecs->systemes()->with_installation($this->id);
    }

    public function data(): InstallationData
    {
        return $this->data;
    }
}
