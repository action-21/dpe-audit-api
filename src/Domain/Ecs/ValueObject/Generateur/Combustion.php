<?php

namespace App\Domain\Ecs\ValueObject\Generateur;

use App\Domain\Common\ValueObject\Pourcentage;
use App\Domain\Ecs\Enum\ModeCombustion;
use Webmozart\Assert\Assert;

final class Combustion
{
    public function __construct(
        public readonly ModeCombustion $mode_combustion,
        public readonly ?bool $presence_ventouse,
        public readonly ?float $pveilleuse,
        public readonly ?float $qp0,
        public readonly ?Pourcentage $rpn,
    ) {}

    public static function create(
        ?ModeCombustion $mode_combustion = null,
        ?bool $presence_ventouse = null,
        ?float $pveilleuse = null,
        ?float $qp0 = null,
        ?Pourcentage $rpn = null,
    ): self {
        Assert::nullOrGreaterThanEq($pveilleuse, 0);
        Assert::nullOrGreaterThan($qp0, 0);
        Assert::nullOrGreaterThan($rpn?->value(), 0);
        Assert::nullOrLessThan($rpn?->value(), 150);

        return new self(
            mode_combustion: $mode_combustion ?? ModeCombustion::STANDARD,
            presence_ventouse: $presence_ventouse,
            pveilleuse: $pveilleuse,
            qp0: $qp0,
            rpn: $rpn,
        );
    }
}
