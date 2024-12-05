<?php

namespace App\Domain\Lnc\Entity;

use App\Domain\Common\Type\Id;
use App\Domain\Lnc\Enum\{EtatIsolation};
use App\Domain\Lnc\Lnc;
use App\Domain\Lnc\Service\MoteurSurfaceDeperditive;
use App\Domain\Lnc\ValueObject\Position;
use Webmozart\Assert\Assert;

final class Paroi
{
    private ?float $aiu = null;
    private ?float $aue = null;

    public function __construct(
        private readonly Id $id,
        private readonly Lnc $local_non_chauffe,
        private string $description,
        private Position $position,
        private float $surface,
        private EtatIsolation $etat_isolation,
    ) {}

    public function update(
        string $description,
        Position $position,
        float $surface,
        EtatIsolation $etat_isolation,
    ): self {
        $this->description = $description;
        $this->position = $position;
        $this->surface = $surface;
        $this->etat_isolation = $etat_isolation;

        $this->controle();
        $this->reinitialise();
        return $this;
    }

    public function controle(): void
    {
        Assert::greaterThan($this->surface, 0);
        Assert::notNull($this->position->mitoyennete);
        Assert::null($this->position->paroi);
    }

    public function reinitialise(): void
    {
        $this->aiu = null;
        $this->aue = null;
    }

    public function calcule_surface_deperditive(MoteurSurfaceDeperditive $moteur): self
    {
        $this->aue = $moteur->calcule_aue_paroi($this);
        $this->aiu = $moteur->calcule_aiu_paroi($this);
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

    public function description(): string
    {
        return $this->description;
    }

    public function position(): Position
    {
        return $this->position;
    }

    public function surface(): float
    {
        return $this->surface;
    }

    public function etat_isolation(): EtatIsolation
    {
        return $this->etat_isolation;
    }

    public function etat_isolation_defaut(): EtatIsolation
    {
        return $this->etat_isolation === EtatIsolation::INCONNU ? EtatIsolation::NON_ISOLE : $this->etat_isolation;
    }

    public function est_isole(): bool
    {
        return $this->etat_isolation_defaut()->est_isole();
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
