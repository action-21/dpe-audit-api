<?php

namespace App\Domain\Chauffage\ValueObject\Generateur;

use App\Domain\Chauffage\Enum\ModeCombustion;
use App\Domain\Common\ValueObject\Pourcentage;
use Webmozart\Assert\Assert;

final class Combustion
{
    public function __construct(
        public readonly ModeCombustion $mode_combustion,
        public readonly ?bool $presence_ventouse,
        public readonly ?bool $presence_regulation_combustion,
        public readonly ?float $pveilleuse,
        public readonly ?float $qp0,
        public readonly ?Pourcentage $rpn,
        public readonly ?Pourcentage $rpint,
        public readonly ?float $tfonc30,
        public readonly ?float $tfonc100,
    ) {}

    public static function create(
        ?ModeCombustion $mode_combustion = null,
        ?bool $presence_ventouse = null,
        ?bool $presence_regulation_combustion = null,
        ?float $pveilleuse = null,
        ?float $qp0 = null,
        ?Pourcentage $rpn = null,
        ?Pourcentage $rpint = null,
        ?float $tfonc30 = null,
        ?float $tfonc100 = null,
    ): self {
        Assert::nullOrGreaterThanEq($pveilleuse, 0);
        Assert::nullOrGreaterThan($qp0, 0);
        Assert::nullOrGreaterThan($rpn?->value(), 0);
        Assert::nullOrLessThan($rpn?->value(), 150);
        Assert::nullOrGreaterThan($rpint?->value(), 0);
        Assert::nullOrLessThan($rpint?->value(), 150);
        Assert::nullOrGreaterThan($tfonc30, 0);
        Assert::nullOrGreaterThan($tfonc100, 0);

        return new self(
            mode_combustion: $mode_combustion ?? ModeCombustion::STANDARD,
            presence_ventouse: $presence_ventouse,
            presence_regulation_combustion: $presence_regulation_combustion,
            pveilleuse: $pveilleuse,
            qp0: $qp0,
            rpn: $rpn,
            rpint: $rpint,
            tfonc30: $tfonc30,
            tfonc100: $tfonc100,
        );
    }
}
