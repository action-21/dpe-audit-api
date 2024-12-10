<?php

namespace App\Api\Chauffage\Payload;

use App\Domain\Chauffage\Enum\TypeCombustion;
use App\Domain\Chauffage\ValueObject\Combustion;
use Symfony\Component\Validator\Constraints as Assert;

final class CombustionPayload
{
    public function __construct(
        public TypeCombustion $type,
        public ?bool $presence_ventouse,
        public ?bool $presence_regulation_combustion,
        #[Assert\PositiveOrZero]
        public ?float $pveilleuse,
        #[Assert\Positive]
        public ?float $qp0,
        #[Assert\Positive]
        public ?float $rpn,
        #[Assert\Positive]
        public ?float $rpint,
        #[Assert\Positive]
        public ?float $tfonc30,
        #[Assert\Positive]
        public ?float $tfonc100,
    ) {}

    public function to(): Combustion
    {
        return Combustion::create(
            type: $this->type,
            presence_ventouse: $this->presence_ventouse,
            presence_regulation_combustion: $this->presence_regulation_combustion,
            pveilleuse: $this->pveilleuse,
            qp0: $this->qp0,
            rpn: $this->rpn,
            rpint: $this->rpint,
            tfonc30: $this->tfonc30,
            tfonc100: $this->tfonc100,
        );
    }
}
