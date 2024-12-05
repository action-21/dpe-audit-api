<?php

namespace App\Api\Refroidissement\Payload;

use App\Api\Refroidissement\Payload\Generateur;
use Symfony\Component\Validator\Constraints as Assert;

final class RefroidissementPayload
{
    public function __construct(
        /** @var (Generateur\AutrePayload|Generateur\ReseauFroidPayload|Generateur\ThermodynamiquePayload)[] */
        #[Assert\All([new Assert\Type([
            Generateur\AutrePayload::class,
            Generateur\ReseauFroidPayload::class,
            Generateur\ThermodynamiquePayload::class,
        ])])]
        #[Assert\Valid]
        public array $generateurs,

        /** @var InstallationPayload[] */
        #[Assert\All([new Assert\Type(InstallationPayload::class)])]
        #[Assert\Valid]
        public array $installations,
    ) {}
}
