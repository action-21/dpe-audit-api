<?php

namespace App\Domain\Logement\Entity;

use App\Domain\Climatisation\{InstallationClimatisationCollection, InstallationClimatisation};
use App\Domain\Common\ValueObject\Id;
use App\Domain\Logement\Logement;
use App\Domain\Logement\ValueObject\Surface;

final class Climatisation
{
    public function __construct(
        private readonly Id $id,
        private readonly Logement $logement,
        private string $description,
        private Surface $surface,
        private InstallationClimatisationCollection $generateur_collection,
    ) {
    }

    public static function create(
        Logement $logement,
        string $description,
        Surface $surface,
    ): self {
        return new self(
            id: Id::create(),
            logement: $logement,
            description: $description,
            surface: $surface,
            generateur_collection: new InstallationClimatisationCollection(),
        );
    }

    public function update(string $description, Surface $surface): self
    {
        $this->description = $description;
        $this->surface = $surface;
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

    public function description(): string
    {
        return $this->description;
    }

    public function surface(): Surface
    {
        return $this->surface;
    }

    public function generateur_collection(): InstallationClimatisationCollection
    {
        return $this->generateur_collection;
    }

    public function bind_generateur(InstallationClimatisation $entity): self
    {
        $this->generateur_collection->add($entity);
        return $this;
    }

    public function detach_generateur(InstallationClimatisation $entity): self
    {
        $this->generateur_collection->removeElement($entity);
        return $this;
    }
}
