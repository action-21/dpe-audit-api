<?php

namespace App\Domain\Production;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Production\Entity\{PanneauPhotovoltaique, PanneauPhotovoltaiqueCollection};

final class Production
{
    public function __construct(
        private readonly Id $id,
        private PanneauPhotovoltaiqueCollection $panneaux_photovoltaiques,
        private ProductionData $data,
    ) {}

    public static function create(): self
    {
        return new self(
            id: Id::create(),
            panneaux_photovoltaiques: new PanneauPhotovoltaiqueCollection(),
            data: ProductionData::create(),
        );
    }

    public function calcule(ProductionData $data): self
    {
        $this->data = $data;
        return $this;
    }

    public function reinitialise(): void
    {
        $this->data = ProductionData::create();
        $this->panneaux_photovoltaiques->reinitialise();
    }

    public function id(): Id
    {
        return $this->id;
    }

    /**
     * @return PanneauPhotovoltaiqueCollection|PanneauPhotovoltaique[]
     */
    public function panneaux_photovoltaiques(): PanneauPhotovoltaiqueCollection
    {
        return $this->panneaux_photovoltaiques;
    }

    public function add_panneau_photovoltaique(PanneauPhotovoltaique $entity): self
    {
        $this->panneaux_photovoltaiques->add($entity);
        $this->reinitialise();
        return $this;
    }

    public function data(): ProductionData
    {
        return $this->data;
    }
}
