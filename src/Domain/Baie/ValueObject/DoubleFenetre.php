<?php

namespace App\Domain\Baie\ValueObject;

use App\Domain\Baie\Enum\TypeBaie;
use App\Domain\Baie\Enum\TypeBaie\{Fenetre, ParoiVitree, PorteFenetre};
use App\Domain\Common\Service\Assert;

final class DoubleFenetre
{
    public function __construct(
        public readonly TypeBaie $type,
        public readonly ?bool $presence_soubassement = null,
        public readonly ?Menuiserie $menuiserie = null,
        public readonly ?float $ug = null,
        public readonly ?float $uw = null,
        public readonly ?float $sw = null,
    ) {}

    public static function create_paroi_vitree(ParoiVitree $type, ?float $ug, ?float $uw, ?float $sw): self
    {
        return new self(type: $type->type_baie(), ug: $ug, uw: $uw, sw: $sw,);
    }

    public static function create_fenetre(
        Fenetre $type,
        Menuiserie $menuiserie,
        ?float $ug,
        ?float $uw,
        ?float $sw,
    ): self {
        return new self(type: $type->type_baie(), menuiserie: $menuiserie, ug: $ug, uw: $uw, sw: $sw,);
    }

    public static function create_porte_fenetre_(
        PorteFenetre $type,
        bool $presence_soubassement,
        Menuiserie $menuiserie,
        ?float $ug,
        ?float $uw,
        ?float $sw,
    ): self {
        return new self(
            type: $type->type_baie(),
            presence_soubassement: $presence_soubassement,
            menuiserie: $menuiserie,
            ug: $ug,
            uw: $uw,
            sw: $sw,
        );
    }

    public function controle(): void
    {
        Assert::positif($this->ug);
        Assert::positif($this->uw);
        Assert::positif($this->sw);
        $this->menuiserie?->controle();
    }
}
