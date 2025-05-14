<?php

namespace App\Domain\Enveloppe\Entity;

use App\Domain\Common\ValueObject\{Annee, Id, Orientation};
use App\Domain\Enveloppe\Data\PlancherBasData;
use App\Domain\Enveloppe\Enum\{InertieParoi, Mitoyennete, TypeParoi};
use App\Domain\Enveloppe\ValueObject\Isolation;
use App\Domain\Enveloppe\Enveloppe;
use App\Domain\Enveloppe\Enum\PlancherBas\TypePlancherBas;
use App\Domain\Enveloppe\ValueObject\PlancherBas\Position;
use Webmozart\Assert\Assert;

final class PlancherBas extends Paroi
{
    public function __construct(
        private readonly Id $id,
        private readonly Enveloppe $enveloppe,
        private string $description,
        private ?TypePlancherBas $type_structure,
        private ?InertieParoi $inertie,
        private ?Annee $annee_construction,
        private ?Annee $annee_renovation,
        private ?float $u0,
        private ?float $u,
        private Position $position,
        private Isolation $isolation,
        private PlancherBasData $data,
    ) {}

    public static function create(
        Id $id,
        Enveloppe $enveloppe,
        string $description,
        ?TypePlancherBas $type_structure,
        ?InertieParoi $inertie,
        ?Annee $annee_construction,
        ?Annee $annee_renovation,
        ?float $u0,
        ?float $u,
        Position $position,
        Isolation $isolation,
    ): self {
        Assert::nullOrGreaterThanEq($u0, 0);
        Assert::nullOrGreaterThanEq($u, 0);

        return new self(
            id: $id,
            enveloppe: $enveloppe,
            description: $description,
            type_structure: $type_structure,
            inertie: $inertie,
            annee_construction: $annee_construction,
            annee_renovation: $annee_renovation,
            u0: $u0,
            u: $u,
            position: $position,
            isolation: $isolation,
            data: PlancherBasData::create(),
        );
    }

    public function reinitialise(): self
    {
        $this->data = PlancherBasData::create();
        return $this;
    }

    public function calcule(PlancherBasData $data): self
    {
        $this->data = $data;
        return $this;
    }

    /** @inheritdoc */
    public function id(): Id
    {
        return $this->id;
    }

    /** @inheritdoc */
    public function enveloppe(): Enveloppe
    {
        return $this->enveloppe;
    }

    /** @inheritdoc */
    public function type_paroi(): TypeParoi
    {
        return TypeParoi::PLANCHER_BAS;
    }

    /** @inheritdoc */
    public function paroi(): ?Paroi
    {
        return null;
    }

    /** @inheritdoc */
    public function local_non_chauffe(): ?Lnc
    {
        return $this->position->local_non_chauffe;
    }

    /** @inheritdoc */
    public function surface(): float
    {
        return $this->position->surface;
    }

    /** @inheritdoc */
    public function mitoyennete(): Mitoyennete
    {
        return $this->position->mitoyennete;
    }

    /** @inheritdoc */
    public function orientation(): ?Orientation
    {
        return null;
    }

    /** @inheritdoc */
    public function pont_thermique_negligeable(): bool
    {
        return $this->type_structure?->pont_thermique_negligeable() ?? false;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function type_structure(): ?TypePlancherBas
    {
        return $this->type_structure;
    }

    public function inertie(): ?InertieParoi
    {
        return $this->inertie;
    }

    public function annee_construction(): ?Annee
    {
        return $this->annee_construction;
    }

    public function annee_renovation(): ?Annee
    {
        return $this->annee_renovation;
    }

    public function u0(): ?float
    {
        return $this->u0;
    }

    public function u(): ?float
    {
        return $this->u;
    }

    public function position(): Position
    {
        return $this->position;
    }

    public function isolation(): Isolation
    {
        return $this->isolation;
    }

    /**
     * @return BaieCollection<Baie>
     */
    public function baies(): BaieCollection
    {
        return $this->enveloppe->baies()->with_paroi($this->id);
    }

    /**
     * @return PorteCollection<Porte>
     */
    public function portes(): PorteCollection
    {
        return $this->enveloppe->portes()->with_paroi($this->id);
    }

    public function data(): PlancherBasData
    {
        return $this->data;
    }
}
