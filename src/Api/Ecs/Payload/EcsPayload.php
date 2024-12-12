<?php

namespace App\Api\Ecs\Payload;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @property GenerateurPayload[] $generateurs
 * @property InstallationPayload[] $installations
 */
final class EcsPayload
{
    public function __construct(
        #[Assert\All([new Assert\Type(GenerateurPayload::class)])]
        #[Assert\Count(min: 1)]
        #[Assert\Valid]
        /** @var GenerateurPayload[] */
        public array $generateurs,
        #[Assert\All([new Assert\Type(InstallationPayload::class)])]
        #[Assert\Count(min: 1)]
        #[Assert\Valid]
        /** @var InstallationPayload[] */
        public array $installations,
    ) {}
}
