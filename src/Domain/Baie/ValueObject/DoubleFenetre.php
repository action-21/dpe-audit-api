<?php

namespace App\Domain\Baie\ValueObject;

use App\Domain\Baie\Enum\TypeBaie;
use Webmozart\Assert\Assert;

final class DoubleFenetre
{
    public function __construct(
        public readonly TypeBaie $type,
        public readonly ?bool $presence_soubassement = null,
        public readonly ?Menuiserie $menuiserie = null,
        public readonly ?Vitrage $vitrage = null,
        public readonly ?float $ug = null,
        public readonly ?float $uw = null,
        public readonly ?float $sw = null,
    ) {}

    public static function create_paroi_vitree(TypeBaie\ParoiVitree $type, ?float $ug, ?float $uw, ?float $sw): self
    {
        Assert::greaterThan($ug, 0);
        Assert::greaterThan($uw, 0);
        Assert::greaterThan($sw, 0);

        return new self(type: $type->type_baie(), ug: $ug, uw: $uw, sw: $sw,);
    }

    public static function create_fenetre(
        TypeBaie\Fenetre $type,
        Menuiserie $menuiserie,
        Vitrage $vitrage,
        ?float $ug,
        ?float $uw,
        ?float $sw,
    ): self {
        Assert::greaterThan($ug, 0);
        Assert::greaterThan($uw, 0);
        Assert::greaterThan($sw, 0);

        return new self(
            type: $type->type_baie(),
            menuiserie: $menuiserie,
            vitrage: $vitrage,
            ug: $ug,
            uw: $uw,
            sw: $sw,
        );
    }

    public static function create_porte_fenetre(
        TypeBaie\PorteFenetre $type,
        bool $presence_soubassement,
        Menuiserie $menuiserie,
        Vitrage $vitrage,
        ?float $ug,
        ?float $uw,
        ?float $sw,
    ): self {
        Assert::greaterThan($ug, 0);
        Assert::greaterThan($uw, 0);
        Assert::greaterThan($sw, 0);

        return new self(
            type: $type->type_baie(),
            presence_soubassement: $presence_soubassement,
            menuiserie: $menuiserie,
            vitrage: $vitrage,
            ug: $ug,
            uw: $uw,
            sw: $sw,
        );
    }
}
