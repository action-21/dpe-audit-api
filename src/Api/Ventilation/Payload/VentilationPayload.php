<?php

namespace App\Api\Ventilation\Payload;

use App\Api\Ventilation\Payload\Generateur;
use Symfony\Component\Validator\Constraints as Assert;

final class VentilationPayload
{
    public function __construct(
        /** @var (Generateur\GenerateurCentralPayload|Generateur\GenerateurDivisePayload)[] */
        #[Assert\All([new Assert\Type([
            Generateur\GenerateurCentralPayload::class,
            Generateur\GenerateurDivisePayload::class,
        ])])]
        #[Assert\Valid]
        public array $generateurs,
        /** @var InstallationPayload[] */
        #[Assert\All([new Assert\Type([InstallationPayload::class])])]
        #[Assert\Count(min: 1)]
        #[Assert\Valid]
        public array $installations,
    ) {}
}
