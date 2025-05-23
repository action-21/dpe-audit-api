<?php

namespace App\Api\Production\Model;

use App\Domain\Production\Production as Entity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @property PanneauPhotovoltaique[] $panneaux_photovoltaiques
 */
final class Production
{
    public function __construct(
        public string $id,

        /** @var PanneauPhotovoltaique[] */
        #[Assert\All([new Assert\Type(PanneauPhotovoltaique::class)])]
        #[Assert\Valid]
        public array $panneaux_photovoltaiques,

        public ?ProductionData $data,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            id: $entity->id(),
            panneaux_photovoltaiques: PanneauPhotovoltaique::from_collection($entity->panneaux_photovoltaiques()),
            data: ProductionData::from($entity),
        );
    }
}
