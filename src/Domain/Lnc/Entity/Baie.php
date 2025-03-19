<?php

namespace App\Domain\Lnc\Entity;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Lnc\Lnc;
use App\Domain\Lnc\Enum\{Materiau, TypeBaie, TypeVitrage};
use App\Domain\Lnc\Service\{MoteurEnsoleillementBaie, MoteurSurfaceDeperditiveBaie};
use App\Domain\Lnc\ValueObject\{EnsoleillementBaie, PositionBaie};
use Webmozart\Assert\Assert;

final class Baie extends Paroi
{
    private ?EnsoleillementBaie $ensoleillement = null;

    public function __construct(
        private readonly Id $id,
        private readonly Lnc $local_non_chauffe,
        private string $description,
        private TypeBaie $type,
        private ?Materiau $materiau,
        private ?TypeVitrage $type_vitrage,
        private ?bool $presence_rupteur_pont_thermique,
        private PositionBaie $position,
    ) {}

    public function controle(): void
    {
        if ($this->position->paroi_id) {
            Assert::notNull($this->paroi());
            Assert::eq($this->paroi()->position()->mitoyennete, $this->position->mitoyennete);
        }
    }

    public function reinitialise(): void
    {
        $this->surface_deperditive = null;
        $this->ensoleillement = null;
    }

    public function calcule_surface_deperditive(MoteurSurfaceDeperditiveBaie $moteur): self
    {
        $this->surface_deperditive = $moteur($this);
        return $this;
    }

    public function calcule_ensoleillement(MoteurEnsoleillementBaie $moteur): self
    {
        $this->ensoleillement = $moteur($this);
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

    public function paroi(): ?ParoiOpaque
    {
        return $this->position->paroi_id
            ? $this->local_non_chauffe->parois()->find($this->position->paroi_id)
            : null;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function type(): TypeBaie
    {
        return $this->type;
    }

    public function materiau(): ?Materiau
    {
        return $this->materiau;
    }

    public function type_vitrage(): ?TypeVitrage
    {
        return $this->type_vitrage;
    }

    public function presence_rupteur_pont_thermique(): ?bool
    {
        return $this->presence_rupteur_pont_thermique;
    }

    public function position(): PositionBaie
    {
        return $this->position;
    }

    public function ensoleillement(): ?EnsoleillementBaie
    {
        return $this->ensoleillement;
    }
}
