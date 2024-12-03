<?php

namespace App\Api\PontThermique\Payload;

use App\Domain\Common\Type\Id;
use App\Domain\PontThermique\ValueObject\Liaison;
use Symfony\Component\Validator\Constraints as Assert;

final class LiaisonRefendMurPayload
{
    public function __construct(
        #[Assert\Uuid]
        public string $mur_id,
        public bool $pont_thermique_partiel,
    ) {}

    public function to(): Liaison
    {
        return Liaison::create_liaison_refend_mur(
            mur_id: Id::from($this->mur_id),
            pont_thermique_partiel: $this->pont_thermique_partiel,
        );
    }
}
