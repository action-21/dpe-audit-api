<?php

namespace App\Api\Lnc\Payload;

use App\Domain\Lnc\Enum\TypeLnc;
use Symfony\Component\Validator\Constraints as Assert;

final class LncPayload
{
    public function __construct(
        #[Assert\Uuid]
        public string $id,
        public string $description,
        public TypeLnc $type,

        /** @var ParoiPayload[] */
        #[Assert\All([new Assert\Type(ParoiPayload::class)])]
        #[Assert\Valid]
        public array $parois,

        /** @var ParoiVitreePayload[] */
        #[Assert\All([new Assert\Type(ParoiVitreePayload::class,)])]
        #[Assert\Valid]
        public array $parois_vitrees,

        /** @var FenetrePayload[] */
        #[Assert\All([new Assert\Type(FenetrePayload::class,)])]
        #[Assert\Valid]
        public array $fenetres,
    ) {}
}
