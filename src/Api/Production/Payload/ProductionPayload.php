<?php

namespace App\Api\Production\Payload;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @property PanneauPhotovoltaiquePayload[] $panneaux_photovoltaiques
 */
final class ProductionPayload
{
    public function __construct(
        /** @var PanneauPhotovoltaiquePayload[] */
        #[Assert\All([new Assert\Type(PanneauPhotovoltaiquePayload::class)])]
        #[Assert\Valid]
        public array $panneaux_photovoltaiques,
    ) {}
}
