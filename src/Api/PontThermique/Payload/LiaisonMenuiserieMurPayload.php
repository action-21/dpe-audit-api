<?php

namespace App\Api\PontThermique\Payload;

use App\Domain\Common\Type\Id;
use App\Domain\PontThermique\ValueObject\Liaison;
use Symfony\Component\Validator\Constraints as Assert;

final class LiaisonMenuiserieMurPayload
{
    public function __construct(
        #[Assert\Uuid]
        public string $mur_id,
        #[Assert\Uuid]
        public string $ouverture_id,
    ) {}

    public function to(): Liaison
    {
        return Liaison::create_liaison_menuiserie_mur(
            mur_id: Id::from($this->mur_id),
            ouverture_id: Id::from($this->ouverture_id),
        );
    }
}
