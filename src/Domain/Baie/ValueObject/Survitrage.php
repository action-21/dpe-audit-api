<?php

namespace App\Domain\Baie\ValueObject;

use App\Domain\Baie\Enum\TypeSurvitrage;
use Webmozart\Assert\Assert;

final class Survitrage
{
    public function __construct(
        public readonly TypeSurvitrage $type_survitrage,
        public readonly ?int $epaisseur_lame,
    ) {}

    public static function create(TypeSurvitrage $type_survitrage, ?int $epaisseur_lame): self
    {
        Assert::greaterThan($epaisseur_lame, 0);
        return new self(type_survitrage: $type_survitrage, epaisseur_lame: $epaisseur_lame);
    }
}
