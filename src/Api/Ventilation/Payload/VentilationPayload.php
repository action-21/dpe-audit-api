<?php

namespace App\Api\Ventilation\Payload;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @property GenerateurPayload[] $generateurs
 * @property InstallationPayload[] $installations
 */
final class VentilationPayload
{
    public function __construct(
        /** @var GenerateurPayload[] */
        #[Assert\All([new Assert\Type(GenerateurPayload::class,)])]
        #[Assert\Valid]
        public array $generateurs,

        /** @var InstallationPayload[] */
        #[Assert\All([new Assert\Type([InstallationPayload::class])])]
        #[Assert\Count(min: 1)]
        #[Assert\Valid]
        public array $installations,
    ) {}
}
