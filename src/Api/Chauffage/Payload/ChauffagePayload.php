<?php

namespace App\Api\Chauffage\Payload;

use Symfony\Component\Validator\Constraints as Assert;

final class ChauffagePayload
{
    public function __construct(
        #[Assert\All([new Assert\Type(GenerateurPayload::class)])]
        #[Assert\Count(min: 1)]
        #[Assert\Valid]
        /** @var GenerateurPayload[] */
        public array $generateurs,
        #[Assert\All([new Assert\Type(EmetteurPayload::class)])]
        #[Assert\Valid]
        /** @var EmetteurPayload[] */
        public array $emetteurs,
        #[Assert\All([new Assert\Type(InstallationPayload::class)])]
        #[Assert\Count(min: 1)]
        #[Assert\Valid]
        /** @var InstallationPayload[] */
        public array $installations,
    ) {}
}
