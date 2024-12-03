<?php

namespace App\Api\PontThermique\Payload;

use App\Domain\Common\Type\Id;
use App\Domain\PontThermique\ValueObject\Liaison;
use Symfony\Component\Validator\Constraints as Assert;

final class LiaisonPlancherHautMurPayload
{
    public function __construct(
        #[Assert\Uuid]
        public string $mur_id,
        #[Assert\Uuid]
        public string $plancher_id,
    ) {}

    public function to(): Liaison
    {
        return Liaison::create_liaison_plancher_haut_mur(
            mur_id: Id::from($this->mur_id),
            plancher_id: Id::from($this->plancher_id),
        );
    }
}
