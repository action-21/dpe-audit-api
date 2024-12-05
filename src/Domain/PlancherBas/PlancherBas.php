<?php

namespace App\Domain\PlancherBas;

use App\Domain\Common\Type\Id;
use App\Domain\Enveloppe\Entity\{Paroi, ParoiCollection};
use App\Domain\Enveloppe\Enveloppe;
use App\Domain\Lnc\Lnc;
use App\Domain\PlancherBas\Enum\{EtatIsolation, Mitoyennete};
use App\Domain\PlancherBas\Service\{MoteurPerformance, MoteurSurfaceDeperditive};
use App\Domain\PlancherBas\ValueObject\{Caracteristique, Performance, Isolation, Position};
use App\Domain\Simulation\Simulation;
use Webmozart\Assert\Assert;

final class PlancherBas implements Paroi
{
    private ?Performance $performance = null;
    private ?float $surface_deperditive = null;

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
        $this->description = $description;
        $this->position = $position;
        $this->caracteristique = $caracteristique;
        $this->isolation = $isolation;

        $this->controle();
        $this->reinitialise();
        return $this;
    }

    public function controle(): void
    {
        Assert::greaterThanEq($this->caracteristique->annee_construction, $this->enveloppe->annee_construction_batiment());
        Assert::greaterThanEq($this->caracteristique->annee_renovation, $this->enveloppe->annee_construction_batiment());
        Assert::greaterThanEq($this->isolation->annee_isolation, $this->enveloppe->annee_construction_batiment());
    }

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

    public function position(): Position
    {
        return $this->position;
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

    public function orientation(): ?float
    {
        return null;
    }

    public function baies(): ParoiCollection
    {
        return $this->enveloppe->parois()->baies()->filter_by_paroi(id: $this->id);
    }

    public function portes(): ParoiCollection
    {
        return $this->enveloppe->parois()->portes()->filter_by_paroi(id: $this->id);
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
        return $this->caracteristique->inertie->est_lourde() ?? true;
    }

    public function est_isole(): bool
    {
        return $this->isolation()->etat_isolation_defaut(
            annee_construction: $this->annee_construction_defaut(),
            mitoyennete: $this->mitoyennete(),
        ) === EtatIsolation::ISOLE;
    }

    public function b(): ?float
    {
        return $this->performance?->b;
    }
}
