<?php

namespace App\Domain\Enveloppe\Entity;

use App\Domain\Common\ValueObject\{Annee, Id, Orientation};
use App\Domain\Enveloppe\Data\MurData;
use App\Domain\Enveloppe\Enum\{InertieParoi, Mitoyennete, TypeParoi};
use App\Domain\Enveloppe\ValueObject\Isolation;
use App\Domain\Enveloppe\Enveloppe;
use App\Domain\Enveloppe\Enum\Mur\{TypeDoublage, TypeMur};
use App\Domain\Enveloppe\ValueObject\Mur\Position;
use Webmozart\Assert\Assert;

final class Mur extends Paroi
{
    public function __construct(
        private readonly Id $id,
        private readonly Enveloppe $enveloppe,
        private string $description,
        private ?TypeMur $type_structure,
        private ?float $epaisseur_structure,
        private TypeDoublage $type_doublage,
        private bool $presence_enduit_isolant,
        private bool $paroi_ancienne,
        private InertieParoi $inertie,
        private ?Annee $annee_construction,
        private ?Annee $annee_renovation,
        private ?float $u0,
        private ?float $u,
        private Position $position,
        private Isolation $isolation,
        private MurData $data,
    ) {}

    public static function create(
        Id $id,
        Enveloppe $enveloppe,
        string $description,
        ?TypeMur $type_structure,
        ?float $epaisseur_structure,
        TypeDoublage $type_doublage,
        bool $presence_enduit_isolant,
        bool $paroi_ancienne,
        InertieParoi $inertie,
        ?Annee $annee_construction,
        ?Annee $annee_renovation,
        ?float $u0,
        ?float $u,
        Position $position,
        Isolation $isolation,
    ): self {
        Assert::nullOrGreaterThanEq($epaisseur_structure, 0);
        Assert::nullOrGreaterThanEq($u0, 0);
        Assert::nullOrGreaterThanEq($u, 0);

        return new self(
            id: $id,
            enveloppe: $enveloppe,
            description: $description,
            type_structure: $type_structure,
            epaisseur_structure: $epaisseur_structure,
            type_doublage: $type_doublage,
            presence_enduit_isolant: $presence_enduit_isolant,
            paroi_ancienne: $paroi_ancienne,
            inertie: $inertie,
            annee_construction: $annee_construction,
            annee_renovation: $annee_renovation,
            u0: $u0,
            u: $u,
            position: $position,
            isolation: $isolation,
            data: MurData::create(),
        );
    }

    public function reinitialise(): self
    {
        $this->data = MurData::create();
        return $this;
    }

    public function calcule(MurData $data): self
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
        return TypeParoi::MUR;
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
    public function orientation(): Orientation
    {
        return $this->position->orientation;
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

    public function type_structure(): ?TypeMur
    {
        return $this->type_structure;
    }

    public function epaisseur_structure(): ?float
    {
        return $this->epaisseur_structure;
    }

    public function type_doublage(): ?TypeDoublage
    {
        return $this->type_doublage;
    }

    public function presence_enduit_isolant(): bool
    {
        return $this->presence_enduit_isolant;
    }

    public function paroi_ancienne(): bool
    {
        return $this->paroi_ancienne;
    }

    public function inertie(): InertieParoi
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

    public function data(): MurData
    {
        return $this->data;
    }
}
