<?php

namespace App\Domain\Refroidissement\Entity;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Refroidissement\Data\InstallationData;
use App\Domain\Refroidissement\Refroidissement;
use Webmozart\Assert\Assert;

final class Installation
{
    public function __construct(
        private readonly Id $id,
        private readonly Refroidissement $refroidissement,
        private string $description,
        private float $surface,
        private InstallationData $data,
    ) {}

    public static function create(
        Id $id,
        Refroidissement $refroidissement,
        string $description,
        float $surface,
    ): self {
        Assert::greaterThan($surface, 0);

        return new self(
            id: $id,
            refroidissement: $refroidissement,
            description: $description,
            surface: $surface,
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

    public function refroidissement(): Refroidissement
    {
        return $this->refroidissement;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function surface(): float
    {
        return $this->surface;
    }

    /**
     * @return SystemeCollection|Systeme[]
     */
    public function systemes(): SystemeCollection
    {
        return $this->refroidissement->systemes()->with_installation($this->id);
    }

    public function data(): InstallationData
    {
        return $this->data;
    }
}
