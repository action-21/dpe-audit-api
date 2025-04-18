<?php

namespace App\Domain\Production\Entity;

use App\Domain\Common\ValueObject\{Id, Inclinaison, Orientation};
use App\Domain\Production\Data\PanneauPhotovoltaiqueData;
use App\Domain\Production\Production;
use Webmozart\Assert\Assert;

final class PanneauPhotovoltaique
{
    public function __construct(
        private readonly Id $id,
        private readonly Production $production,
        private string $description,
        private Orientation $orientation,
        private Inclinaison $inclinaison,
        private int $modules,
        private ?float $surface,
        private PanneauPhotovoltaiqueData $data,
    ) {}

    public static function create(
        Id $id,
        Production $production,
        string $description,
        Orientation $orientation,
        Inclinaison $inclinaison,
        int $modules,
        ?float $surface,
    ): self {
        Assert::greaterThan($modules, 0);
        Assert::nullOrGreaterThan($surface, 0);

        return new self(
            id: $id,
            production: $production,
            description: $description,
            orientation: $orientation,
            inclinaison: $inclinaison,
            modules: $modules,
            surface: $surface,
            data: PanneauPhotovoltaiqueData::create(),
        );
    }

    public function calcule(PanneauPhotovoltaiqueData $data): self
    {
        $this->data = $data;
        return $this;
    }

    public function reinitialise(): self
    {
        $this->data = PanneauPhotovoltaiqueData::create();
        return $this;
    }

    public function id(): Id
    {
        return $this->id;
    }

    public function production(): Production
    {
        return $this->production;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function inclinaison(): Inclinaison
    {
        return $this->inclinaison;
    }

    public function orientation(): Orientation
    {
        return $this->orientation;
    }

    public function modules(): int
    {
        return $this->modules;
    }

    public function surface(): ?float
    {
        return $this->surface;
    }

    public function data(): PanneauPhotovoltaiqueData
    {
        return $this->data;
    }
}
