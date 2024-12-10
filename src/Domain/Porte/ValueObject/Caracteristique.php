<?php

namespace App\Domain\Porte\ValueObject;

use App\Domain\Porte\Enum\EtatIsolation;
use Webmozart\Assert\Assert;

final class Caracteristique
{
    public function __construct(
        public readonly float $surface,
        public readonly bool $presence_sas,
        public readonly EtatIsolation $isolation,
        public readonly Menuiserie $menuiserie,
        public readonly Vitrage $vitrage,
        public readonly ?int $annee_installation,
        public readonly ?float $u,
    ) {}

    public static function create(
        float $surface,
        bool $presence_sas,
        EtatIsolation $isolation,
        Menuiserie $menuiserie,
        ?Vitrage $vitrage,
        ?int $annee_installation,
        ?float $u,
    ): self {
        Assert::greaterThanEq($surface, 0);
        Assert::greaterThanEq($u, 0);
        Assert::nullOrLessThanEq($annee_installation, (int) \date('Y'));

        return new self(
            surface: $surface,
            presence_sas: $presence_sas,
            isolation: $isolation,
            menuiserie: $menuiserie,
            vitrage: $vitrage ?? new Vitrage(),
            annee_installation: $annee_installation,
            u: $u,
        );
    }
}
