<?php

namespace App\Domain\Logement\Entity;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Logement\Logement;
use App\Domain\Logement\ValueObject\Surface;
use App\Domain\Ventilation\InstallationVentilation;

final class Ventilation
{
    public function __construct(
        private readonly Id $id,
        private readonly Logement $logement,
        private InstallationVentilation $reference,
        private string $description,
        private Surface $surface,
    ) {
    }

    public static function create(
        Logement $logement,
        InstallationVentilation $reference,
        string $description,
        Surface $surface,
    ): self {
        return new self(
            id: Id::create(),
            logement: $logement,
            reference: $reference,
            description: $description,
            surface: $surface,
        );
    }

    public function update(string $description, Surface $surface): self
    {
        $this->description = $description;
        $this->surface = $surface;
        return $this;
    }

    public function bind(InstallationVentilation $reference): self
    {
        $this->reference = $reference;
        return $this;
    }

    public function id(): Id
    {
        return $this->id;
    }

    public function logement(): Logement
    {
        return $this->logement;
    }

    public function reference(): InstallationVentilation
    {
        return $this->reference;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function surface(): Surface
    {
        return $this->surface;
    }
}
