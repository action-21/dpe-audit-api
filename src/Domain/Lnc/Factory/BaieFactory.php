<?php

namespace App\Domain\Lnc\Factory;

use App\Domain\Common\Type\Id;
use App\Domain\Lnc\Entity\Baie;
use App\Domain\Lnc\Enum\TypeBaie;
use App\Domain\Lnc\Lnc;
use App\Domain\Lnc\ValueObject\{Menuiserie, Position};
use Webmozart\Assert\Assert;

final class BaieFactory
{
    private Id $id;
    private Lnc $local_non_chauffe;
    private string $description;
    private float $surface;
    private float $inclinaison;
    private Position $position;

    public function initialise(
        Id $id,
        Lnc $local_non_chauffe,
        string $description,
        float $surface,
        float $inclinaison,
        Position $position,
    ): self {
        Assert::greaterThan($surface, 0);
        Assert::greaterThanEq($inclinaison, 0);
        Assert::lessThanEq($inclinaison, 90);

        $this->id = $id;
        $this->local_non_chauffe = $local_non_chauffe;
        $this->description = $description;
        $this->surface = $surface;
        $this->inclinaison = $inclinaison;
        $this->position = $position;
        return $this;
    }

    private function build(TypeBaie $type, ?Menuiserie $menuiserie = null,): Baie
    {
        return new Baie(
            id: $this->id,
            local_non_chauffe: $this->local_non_chauffe,
            description: $this->description,
            surface: $this->surface,
            inclinaison: $this->inclinaison,
            position: $this->position,
            type: $type,
            menuiserie: $menuiserie,
        );
    }

    public function build_paroi_polycarbonate(): Baie
    {
        return $this->build(type: TypeBaie::POLYCARBONATE);
    }

    public function build_fenetre(TypeBaie\TypeBaieFenetre $type, Menuiserie $menuiserie,): Baie
    {
        return $this->build(type: $type->to(), menuiserie: $menuiserie);
    }
}
