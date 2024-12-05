<?php

namespace App\Api\Refroidissement\Payload;

use Symfony\Component\Validator\Constraints as Assert;

final class RefroidissementPayload
{
    public function __construct(
        /** @var ClimatiseurPayload[] */
        #[Assert\All([new Assert\Type(ClimatiseurPayload::class,)])]
        #[Assert\Valid]
        public array $climatiseurs,

        /** @var ReseauFroidPayload[] */
        #[Assert\All([new Assert\Type(ReseauFroidPayload::class,)])]
        #[Assert\Valid]
        public array $reseaux_froid,

        /** @var InstallationPayload[] */
        #[Assert\All([new Assert\Type(InstallationPayload::class)])]
        #[Assert\Valid]
        public array $installations,
    ) {}
}
