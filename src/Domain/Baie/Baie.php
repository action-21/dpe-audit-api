<?php

namespace App\Domain\Baie;

use App\Domain\Baie\Entity\{MasqueLointain, MasqueLointainCollection, MasqueProche, MasqueProcheCollection};
use App\Domain\Baie\Enum\Mitoyennete;
use App\Domain\Baie\Service\{MoteurEnsoleillement, MoteurPerformance};
use App\Domain\Baie\ValueObject\{Caracteristique, DoubleFenetre, EnsoleillementCollection, Performance, Position};
use App\Domain\Common\ValueObject\Id;
use App\Domain\Enveloppe\Entity\Paroi;
use App\Domain\Enveloppe\Enveloppe;
use App\Domain\Lnc\Lnc;
use Webmozart\Assert\Assert;

final class Baie implements Paroi
{
    private ?Performance $performance = null;
    private ?EnsoleillementCollection $ensoleillement = null;

    public function __construct(
        private readonly Id $id,
        private readonly Enveloppe $enveloppe,
        private string $description,
        private Position $position,
        private Caracteristique $caracteristique,
        private ?DoubleFenetre $double_fenetre,
        private MasqueProcheCollection $masques_proches,
        private MasqueLointainCollection $masques_lointains
    ) {}

    public static function create(
        Id $id,
        Enveloppe $enveloppe,
        string $description,
        Position $position,
        Caracteristique $caracteristique,
        ?DoubleFenetre $double_fenetre,
    ): self {
        Assert::nullOrGreaterThanEq($caracteristique->annee_installation, $enveloppe->annee_construction_batiment());

        return new self(
            id: $id,
            enveloppe: $enveloppe,
            description: $description,
            position: $position,
            caracteristique: $caracteristique,
            double_fenetre: $double_fenetre,
            masques_proches: new MasqueProcheCollection(),
            masques_lointains: new MasqueLointainCollection()
        );
    }

    public function controle(): void
    {
        Assert::nullOrGreaterThanEq($this->caracteristique->annee_installation, $this->enveloppe->annee_construction_batiment());
    }

    public function reinitialise(): void
    {
        $this->performance = null;
        $this->ensoleillement = null;
    }

    public function calcule_performance(MoteurPerformance $moteur): self
    {
        $this->performance = $moteur->calcule_performance($this);
        return $this;
    }

    public function calcule_ensoleillement(MoteurEnsoleillement $moteur): self
    {
        $this->ensoleillement = $moteur->calcule_ensoleillement($this);
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

    public function paroi(): ?Paroi
    {
        return $this->position->paroi_id ? $this->enveloppe->parois()->get(id: $this->position->paroi_id) : null;
    }

    public function local_non_chauffe(): ?Lnc
    {
        if ($this->paroi())
            return $this->paroi()->local_non_chauffe();

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

    public function caracteristique(): Caracteristique
    {
        return $this->caracteristique;
    }

    public function mitoyennete(): Mitoyennete
    {
        return $this->paroi()?->mitoyennete() ? Mitoyennete::from($this->paroi()->mitoyennete()->value) : $this->position->mitoyennete;
    }

    public function orientation(): ?float
    {
        return $this->paroi()?->orientation() ?? $this->position->orientation;
    }

    public function presence_joint_menuiserie(): bool
    {
        return ($this->caracteristique->menuiserie?->presence_joint ?? $this->double_fenetre?->menuiserie?->presence_joint) ?? false;
    }

    public function est_isole(): bool
    {
        return $this->caracteristique->vitrage?->type_vitrage->est_isole() ?? false;
    }

    public function surface_deperditive(): float
    {
        return $this->mitoyennete() === Mitoyennete::LOCAL_RESIDENTIEL ? 0 : $this->caracteristique->surface;
    }

    public function double_fenetre(): ?DoubleFenetre
    {
        return $this->double_fenetre;
    }

    public function masques_proches(): MasqueProcheCollection
    {
        return $this->masques_proches;
    }

    public function add_masque_proche(MasqueProche $entity): self
    {
        $this->masques_proches->add($entity);
        return $this;
    }

    public function masques_lointains(): MasqueLointainCollection
    {
        return $this->masques_lointains;
    }

    public function add_masque_lointain(MasqueLointain $entity): self
    {
        Assert::notNull($this->orientation());

        $this->masques_lointains->add($entity);
        return $this;
    }

    public function performance(): ?Performance
    {
        return $this->performance;
    }

    public function ensoleillement(): ?EnsoleillementCollection
    {
        return $this->ensoleillement;
    }

    public function b(): ?float
    {
        return $this->performance?->b;
    }
}
