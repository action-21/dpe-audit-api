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

        #[Assert\All([new Assert\Type(PanneauPhotovoltaique::class)])]
        #[Assert\Valid]
        public readonly array $panneaux_photovoltaiques,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            panneaux_photovoltaiques: PanneauPhotovoltaique::from_collection($entity->panneaux_photovoltaiques()),
        );
    }
}
