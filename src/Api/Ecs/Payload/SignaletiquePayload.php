<?php

namespace App\Api\Ecs\Payload;

use App\Domain\Ecs\Enum\{EnergieGenerateur, LabelGenerateur, TypeChaudiere, TypeGenerateur};
use Symfony\Component\Validator\Constraints as Assert;

final class SignaletiquePayload
{
    public function __construct(
        public TypeGenerateur $type,
        public EnergieGenerateur $energie,
        #[Assert\PositiveOrZero]
        public int $volume_stockage,
        public bool $position_volume_chauffe,
        public bool $generateur_collectif,
        public ?TypeChaudiere $type_chaudiere,
        public ?LabelGenerateur $label,
        public ?bool $presence_ventouse,
        #[Assert\Positive]
        public ?float $pn,
        #[Assert\Positive]
        public ?float $rpn,
        #[Assert\Positive]
        public ?float $qp0,
        #[Assert\PositiveOrZero]
        public ?float $pveilleuse,
        #[Assert\Positive]
        public ?float $cop,
    ) {}
}
