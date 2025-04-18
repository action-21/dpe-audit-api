<?php

namespace App\Domain\Enveloppe\ValueObject\Baie;

use App\Domain\Enveloppe\Enum\Baie\TypeSurvitrage;
use Webmozart\Assert\Assert;

final class Survitrage
{
    public function __construct(
        public readonly ?TypeSurvitrage $type_survitrage,
        public readonly ?float $epaisseur_lame,
    ) {}

    public static function create(?TypeSurvitrage $type_survitrage, ?float $epaisseur_lame): self
    {
        Assert::greaterThan($epaisseur_lame, 0);
        return new self(type_survitrage: $type_survitrage, epaisseur_lame: $epaisseur_lame);
    }
}
