<?php

namespace App\Domain\Baie\ValueObject;

use App\Domain\Baie\Enum\TypeSurvitrage;
use App\Domain\Common\Service\Assert;

final class Survitrage
{
    public function __construct(
        public readonly TypeSurvitrage $type_survitrage,
        public readonly ?int $epaisseur_lame,
    ) {}

    public static function create(TypeSurvitrage $type_survitrage, ?int $epaisseur_lame): self
    {
        Assert::positif($epaisseur_lame);
        return new self(type_survitrage: $type_survitrage, epaisseur_lame: $epaisseur_lame);
    }
}
