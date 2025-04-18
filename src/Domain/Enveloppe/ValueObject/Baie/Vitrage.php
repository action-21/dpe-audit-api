<?php

namespace App\Domain\Enveloppe\ValueObject\Baie;

use App\Domain\Enveloppe\Enum\Baie\{NatureGazLame, TypeVitrage};
use Webmozart\Assert\Assert;

final class Vitrage
{
    public function __construct(
        public readonly ?TypeVitrage $type_vitrage,
        public readonly ?Survitrage $survitrage,
        public readonly ?NatureGazLame $nature_gaz_lame,
        public readonly ?int $epaisseur_lame,
    ) {}

    public static function create(
        ?TypeVitrage $type_vitrage = null,
        ?Survitrage $survitrage = null,
        ?NatureGazLame $nature_gaz_lame = null,
        ?int $epaisseur_lame = null,
    ): self {
        $value = new self(
            type_vitrage: $type_vitrage,
            survitrage: $survitrage,
            nature_gaz_lame: null,
            epaisseur_lame: null,
        );
        return $type_vitrage === TypeVitrage::SIMPLE_VITRAGE ? $value : $value->with_vitrage_complexe(
            nature_gaz_lame: $nature_gaz_lame,
            epaisseur_lame: $epaisseur_lame,
        );
    }

    public function with_vitrage_complexe(?NatureGazLame $nature_gaz_lame, ?float $epaisseur_lame): self
    {
        Assert::greaterThan($epaisseur_lame, 0);

        return new self(
            type_vitrage: $this->type_vitrage,
            survitrage: $this->survitrage,
            nature_gaz_lame: $nature_gaz_lame,
            epaisseur_lame: $epaisseur_lame,
        );
    }
}
