<?php

namespace App\Domain\Chauffage\ValueObject;

use App\Domain\Chauffage\Enum\TypeCombustion;
use Webmozart\Assert\Assert;

final class Combustion
{
    public function __construct(
        public readonly TypeCombustion $type,
        public readonly ?bool $presence_ventouse,
        public readonly ?bool $presence_regulation_combustion,
        public readonly ?float $pveilleuse,
        public readonly ?float $qp0,
        public readonly ?float $rpn,
        public readonly ?float $rpint,
        public readonly ?float $tfonc30,
        public readonly ?float $tfonc100,
    ) {}

    public static function create(
        TypeCombustion $type,
        ?bool $presence_ventouse = null,
        ?bool $presence_regulation_combustion = null,
        ?float $pveilleuse = null,
        ?float $qp0 = null,
        ?float $rpn = null,
        ?float $rpint = null,
        ?float $tfonc30 = null,
        ?float $tfonc100 = null,
    ): self {
        Assert::nullOrGreaterThanEq($pveilleuse, 0);
        Assert::nullOrGreaterThan($qp0, 0);
        Assert::nullOrGreaterThan($rpn, 0);
        Assert::nullOrGreaterThan($rpint, 0);
        Assert::nullOrGreaterThan($tfonc30, 0);
        Assert::nullOrGreaterThan($tfonc100, 0);

        return new self(
            type: $type,
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

    public static function default(): self
    {
        return new self(
            type: TypeCombustion::STANDARD,
            presence_ventouse: null,
            presence_regulation_combustion: null,
            pveilleuse: null,
            qp0: null,
            rpn: null,
            rpint: null,
            tfonc30: null,
            tfonc100: null,
        );
    }
}
