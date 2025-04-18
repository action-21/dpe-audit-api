<?php

namespace App\Domain\Ventilation\Entity;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Ventilation\Data\InstallationData;
use App\Domain\Ventilation\Ventilation;
use Webmozart\Assert\Assert;

final class Installation
{
    public function __construct(
        private readonly Id $id,
        private readonly Ventilation $ventilation,
        private string $description,
        private float $surface,
        private InstallationData $data,
    ) {}

    public static function create(
        Id $id,
        Ventilation $ventilation,
        string $description,
        float $surface,
    ): self {
        Assert::greaterThan($surface, 0);

        return new self(
            id: $id,
            ventilation: $ventilation,
            description: $description,
            surface: $surface,
            data: InstallationData::create(),
        );
    }

    public function calcule(InstallationData $data): self
    {
        $this->data = $data;
        return $this;
    }

    public function reinitialise(): void
    {
        $this->data = InstallationData::create();
    }

    public function id(): Id
    {
        return $this->id;
    }

    public function ventilation(): Ventilation
    {
        return $this->ventilation;
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
        return $this->ventilation->systemes()->with_installation($this->id);
    }

    public function data(): InstallationData
    {
        return $this->data;
    }
}
