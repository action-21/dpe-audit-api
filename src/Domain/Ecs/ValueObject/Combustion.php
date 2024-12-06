<?php

namespace App\Domain\Ecs\ValueObject;

use App\Domain\Ecs\Enum\TypeCombustion;
use Webmozart\Assert\Assert;

final class Combustion
{
    public function __construct(
        public readonly TypeCombustion $type,
        public readonly ?bool $presence_ventouse,
        public readonly ?float $pveilleuse,
        public readonly ?float $qp0,
        public readonly ?float $rpn,
    ) {}

    public static function create(
        TypeCombustion $type,
        ?bool $presence_ventouse,
        ?float $pveilleuse,
        ?float $qp0,
        ?float $rpn,
    ): self {
        Assert::nullOrGreaterThanEq($pveilleuse, 0);
        Assert::nullOrGreaterThan($qp0, 0);
        Assert::nullOrGreaterThan($rpn, 0);

        return new self(
            type: $type,
            presence_ventouse: $presence_ventouse,
            pveilleuse: $pveilleuse,
            qp0: $qp0,
            rpn: $rpn,
        );
    }

    public static function default(): self
    {
        return new self(
            type: TypeCombustion::STANDARD,
            presence_ventouse: null,
            pveilleuse: null,
            qp0: null,
            rpn: null,
        );
    }
}
