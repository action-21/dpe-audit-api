<?php

namespace App\Domain\Baie\ValueObject;

use App\Domain\Baie\Enum\{NatureGazLame, TypeVitrage};
use Webmozart\Assert\Assert;

final class Vitrage
{
    public function __construct(
        public readonly TypeVitrage $type_vitrage,
        public readonly ?Survitrage $survitrage = null,
        public readonly ?NatureGazLame $nature_gaz_lame = null,
        public readonly ?int $epaisseur_lame = null,
    ) {}

    public static function create(
        TypeVitrage $type_vitrage,
        ?Survitrage $survitrage,
        ?NatureGazLame $nature_gaz_lame,
        ?int $epaisseur_lame,
    ): self {
        Assert::greaterThan($epaisseur_lame, 0);

        return $type_vitrage === TypeVitrage::SIMPLE_VITRAGE
            ? new self(
                type_vitrage: $type_vitrage,
                survitrage: $survitrage,
            )
            : new self(
                type_vitrage: $type_vitrage,
                survitrage: $survitrage,
                nature_gaz_lame: $nature_gaz_lame ?? NatureGazLame::INCONNU,
                epaisseur_lame: $epaisseur_lame,
            );
    }
}
