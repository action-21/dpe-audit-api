<?php

namespace App\Domain\Lnc\Entity;

use App\Domain\Common\Type\Id;
use App\Domain\Lnc\Lnc;
use App\Domain\Lnc\Enum\{EtatIsolation, TypeBaie, TypeLnc};
use App\Domain\Lnc\Enum\TypeBaie\TypeBaieFenetre;
use App\Domain\Lnc\Service\{MoteurEnsoleillement, MoteurSurfaceDeperditive};
use App\Domain\Lnc\ValueObject\{EnsoleillementBaieCollection, Menuiserie, Position};
use Webmozart\Assert\Assert;

final class Baie
{
    private ?float $aiu = null;
    private ?float $aue = null;
    private ?EnsoleillementBaieCollection $ensoleillement = null;

    public function __construct(
        private readonly Id $id,
        private readonly Lnc $local_non_chauffe,
        private string $description,
        private Position $position,
        private TypeBaie $type,
        private float $surface,
        private float $inclinaison,
        private ?Menuiserie $menuiserie = null,
    ) {}

    public function set_paroi_polycarbonate(): self
    {
        $this->type = TypeBaie::POLYCARBONATE;
        $this->menuiserie = null;
        $this->reinitialise();
        return $this;
    }

    public function set_fenetre(TypeBaieFenetre $type, Menuiserie $menuiserie): self
    {
        $this->type = $type->to();
        $this->menuiserie = $menuiserie;
        $this->reinitialise();
        return $this;
    }

    public function update(string $description, float $surface, float $inclinaison, Position $position,): self
    {
        Assert::greaterThan($surface, 0);
        Assert::greaterThanEq($inclinaison, 0);
        Assert::lessThanEq($inclinaison, 90);

        $this->description = $description;
        $this->surface = $surface;
        $this->inclinaison = $inclinaison;
        $this->position = $position;

        $this->reinitialise();
        return $this;
    }

    public function controle(): void {}

    public function reinitialise(): void
    {
        $this->aiu = null;
        $this->aue = null;
        $this->ensoleillement = null;
    }

    public function calcule_surface_deperditive(MoteurSurfaceDeperditive $moteur): self
    {
        $this->aue = $moteur->calcule_aue_baie($this);
        $this->aiu = $moteur->calcule_aiu_baie($this);
        return $this;
    }

    public function calcule_ensoleillement(MoteurEnsoleillement $moteur): self
    {
        $this->ensoleillement = $moteur->calcule_ensoleillement_baie($this);
        return $this;
    }

    public function id(): Id
    {
        return $this->id;
    }

    public function local_non_chauffe(): Lnc
    {
        return $this->local_non_chauffe;
    }

    public function type_lnc(): TypeLnc
    {
        return $this->local_non_chauffe->type();
    }

    public function paroi(): ?Paroi
    {
        return $this->position->paroi;
    }

    public function position(): Position
    {
        return $this->position;
    }

    public function type(): TypeBaie
    {
        return $this->type;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function surface(): float
    {
        return $this->surface;
    }

    public function inclinaison(): float
    {
        return $this->inclinaison;
    }

    public function menuiserie(): ?Menuiserie
    {
        return $this->menuiserie;
    }

    public function etat_isolation(): EtatIsolation
    {
        return $this->menuiserie?->etat_isolation() ?? EtatIsolation::NON_ISOLE;
    }

    public function ensoleillement(): ?EnsoleillementBaieCollection
    {
        return $this->ensoleillement;
    }

    public function aiu(): ?float
    {
        return $this->aiu;
    }

    public function aue(): ?float
    {
        return $this->aue;
    }
}
