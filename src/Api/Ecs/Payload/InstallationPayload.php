<?php

namespace App\Api\Ecs\Payload;

use Symfony\Component\Validator\Constraints as Assert;

final class InstallationPayload
{
    public function __construct(
        #[Assert\Uuid]
        public string $id,
        public string $description,
        #[Assert\Positive]
        public float $surface,
        #[Assert\Valid]
        public ?SolairePayload $solaire,
        #[Assert\All([new Assert\Type(SystemePayload::class)])]
        #[Assert\Count(min: 1)]
        #[Assert\Valid]
        /** @var SystemePayload[] */
        public array $systemes,
    ) {}
}
