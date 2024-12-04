<?php

namespace App\Domain\Mur;

use App\Domain\Common\Type\Id;
use App\Domain\Enveloppe\Entity\Paroi;
use App\Domain\Enveloppe\Enveloppe;
use App\Domain\Lnc\Lnc;
use App\Domain\Mur\Enum\{EtatIsolation, Mitoyennete};
use App\Domain\Mur\Service\{MoteurPerformance, MoteurSurfaceDeperditive};
use App\Domain\Mur\ValueObject\{Caracteristique, Performance, Isolation, Position};
use App\Domain\Simulation\Simulation;
use Webmozart\Assert\Assert;

final class Mur implements Paroi
{
    private ?float $surface_deperditive = null;
    private ?Performance $performance = null;

    public function __construct(
        private readonly Id $id,
        private readonly Enveloppe $enveloppe,
        private string $description,
        private Position $position,
        private Caracteristique $caracteristique,
        private Isolation $isolation,
    ) {}

    public function update(
        string $description,
        Position $position,
        Caracteristique $caracteristique,
        Isolation $isolation,
    ): self {
        Assert::greaterThanEq($caracteristique->annee_construction, $this->enveloppe->annee_construction_batiment());
        Assert::greaterThanEq($caracteristique->annee_renovation, $this->enveloppe->annee_construction_batiment());

        $this->description = $description;
        $this->position = $position;
        $this->caracteristique = $caracteristique;
        $this->isolation = $isolation;

        $this->reinitialise();
        return $this;
    }

    public function controle(): void {}

    public function reinitialise(): void
    {
        $this->performance = null;
        $this->surface_deperditive = null;
    }

    public function calcule_surface_deperditive(MoteurSurfaceDeperditive $moteur): self
    {
        $this->surface_deperditive = $moteur->calcule_surface_deperditive($this);
        return $this;
    }

    public function calcule_performance(MoteurPerformance $moteur, Simulation $simulation): self
    {
        $this->performance = $moteur->calcule_performance($this, $simulation);
        return $this;
    }

    public function id(): Id
    {
        return $this->id;
    }

    public function enveloppe(): Enveloppe
    {
        return $this->enveloppe;
    }

    public function position(): Position
    {
        return $this->position;
    }

    public function local_non_chauffe(): ?Lnc
    {
        return $this->position->local_non_chauffe_id
            ? $this->enveloppe->locaux_non_chauffes()->find(id: $this->position->local_non_chauffe_id)
            : null;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function mitoyennete(): Mitoyennete
    {
        return $this->position->mitoyennete;
    }

    public function caracteristique(): Caracteristique
    {
        return $this->caracteristique;
    }

    public function isolation(): Isolation
    {
        return $this->isolation;
    }

    public function orientation(): float
    {
        return $this->position->orientation;
    }

    public function performance(): ?Performance
    {
        return $this->performance;
    }

    public function surface_deperditive(): ?float
    {
        return $this->surface_deperditive;
    }

    public function annee_construction_defaut(): int
    {
        return $this->caracteristique->annee_construction ?? $this->enveloppe->audit()->annee_construction_batiment();
    }

    public function est_lourd(): bool
    {
        return $this->caracteristique->inertie->est_lourde() ?? false;
    }

    public function est_isole(): bool
    {
        return $this->isolation()->etat_isolation_defaut(
            annee_construction: $this->annee_construction_defaut()
        ) === EtatIsolation::ISOLE;
    }

    public function b(): ?float
    {
        return $this->performance?->b;
    }
}
