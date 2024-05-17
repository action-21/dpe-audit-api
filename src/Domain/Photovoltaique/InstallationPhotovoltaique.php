<?php

namespace App\Domain\Photovoltaique;

use App\Domain\Batiment\Batiment;
use App\Domain\Common\ValueObject\Id;
use App\Domain\Photovoltaique\Entity\{PanneauPhotovoltaique, PanneauPhotovoltaiqueCollection};

final class InstallationPhotovoltaique
{
    public function __construct(
        private readonly Id $id,
        private readonly Batiment $batiment,
        private PanneauPhotovoltaiqueCollection $panneau_photovoltaique_collection,
    ) {
    }

    public static function create(Batiment $batiment): self
    {
        return new self(
            id: Id::create(),
            batiment: $batiment,
            panneau_photovoltaique_collection: new PanneauPhotovoltaiqueCollection,
        );
    }

    public function id(): Id
    {
        return $this->id;
    }

    public function batiment(): Batiment
    {
        return $this->batiment;
    }

    public function surface_capteurs(): float
    {
        return $this->panneau_photovoltaique_collection->surface_capteurs();
    }

    public function panneau_photovoltaique_collection(): PanneauPhotovoltaiqueCollection
    {
        return $this->panneau_photovoltaique_collection;
    }

    public function get_panneau_photovoltaique(Id $id): ?PanneauPhotovoltaique
    {
        return $this->panneau_photovoltaique_collection->find($id);
    }

    public function add_panneau_photovoltaique(PanneauPhotovoltaique $entity): self
    {
        $this->panneau_photovoltaique_collection->add($entity);
        return $this;
    }

    public function remove_panneau_photovoltaique(PanneauPhotovoltaique $entity): self
    {
        $this->panneau_photovoltaique_collection->removeElement($entity);
        return $this;
    }
}
