<?php

namespace App\Api\Lnc\Payload;

use App\Domain\Lnc\Enum\TypeLnc;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @property ParoiPayload[] $parois
 * @property BaiePayload[] $baies
 */
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

        /** @var BaiePayload[] */
        #[Assert\All([new Assert\Type(BaiePayload::class,)])]
        #[Assert\Valid]
        public array $baies,
    ) {}

    #[Assert\IsTrue]
    public function isValid(): bool
    {
        foreach ($this->baies as $baie) {
            if ($baie->position->paroi_id) {
                foreach ($this->parois as $paroi) {
                    if ($paroi->id === $baie->position->paroi_id) {
                        return true;
                    }
                }
                return false;
            }
        }
        return true;
    }
}
