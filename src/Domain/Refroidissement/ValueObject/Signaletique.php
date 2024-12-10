<?php

namespace App\Domain\Refroidissement\ValueObject;

use App\Domain\Refroidissement\Enum\{EnergieGenerateur, TypeGenerateur};
use Webmozart\Assert\Assert;

final class Signaletique
{
    public function __construct(
        public readonly TypeGenerateur $type_generateur,
        public readonly EnergieGenerateur $energie_generateur,
        public readonly ?float $seer = null,
    ) {}

    private function merge(?float $seer = null): self
    {
        return new self(
            type_generateur: $this->type_generateur,
            energie_generateur: $this->energie_generateur,
            seer: $seer ?? $this->seer,
        );
    }

    public static function create_climatiseur(
        TypeGenerateur\Climatiseur $type_generateur,
        EnergieGenerateur\Climatiseur $energie_generateur,
    ): self {
        return new self(
            type_generateur: $type_generateur = $type_generateur->to(),
            energie_generateur: $energie_generateur->to(),
        );
    }

    public static function create_pac(
        TypeGenerateur\Pac $type_generateur,
    ): self {
        return new self(
            type_generateur: $type_generateur = $type_generateur->to(),
            energie_generateur: EnergieGenerateur::ELECTRICITE,
        );
    }

    public static function create_reseau_froid(): self
    {
        return new self(
            type_generateur: TypeGenerateur::RESEAU_FROID,
            energie_generateur: EnergieGenerateur::RESEAU_FROID,
        );
    }

    public function with_seer(?float $seer): self
    {
        Assert::nullOrGreaterThan($seer, 0);
        return $this->type_generateur !== TypeGenerateur::RESEAU_FROID ? $this->merge(seer: $seer) : $this;
    }
}
