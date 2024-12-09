<?php

namespace App\Api\Ecs\Payload;

use App\Domain\Ecs\Enum\TypeCombustion;
use App\Domain\Ecs\ValueObject\Combustion;
use Symfony\Component\Validator\Constraints as Assert;

final class CombustionPayload
{
    public function __construct(
        public TypeCombustion $type,
        public ?bool $presence_ventouse,
        #[Assert\PositiveOrZero]
        public ?float $pveilleuse,
        #[Assert\Positive]
        public ?float $qp0,
        #[Assert\Positive]
        public ?float $rpn,
    ) {}

    public function to(): Combustion
    {
        return Combustion::create(
            type: $this->type,
            presence_ventouse: $this->presence_ventouse,
            pveilleuse: $this->pveilleuse,
            qp0: $this->qp0,
            rpn: $this->rpn,
        );
    }
}
