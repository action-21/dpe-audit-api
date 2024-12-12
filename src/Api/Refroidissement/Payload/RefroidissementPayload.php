<?php

namespace App\Api\Refroidissement\Payload;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @property GenerateurPayload[] $generateurs
 * @property InstallationPayload[] $installations
 */
final class RefroidissementPayload
{
    public function __construct(
        /** @var GenerateurPayload[] */
        #[Assert\All([new Assert\Type(GenerateurPayload::class,)])]
        #[Assert\Valid]
        public array $generateurs,

        /** @var InstallationPayload[] */
        #[Assert\All([new Assert\Type(InstallationPayload::class)])]
        #[Assert\Valid]
        public array $installations,
    ) {}
}
