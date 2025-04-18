<?php

namespace App\Domain\Enveloppe\Entity;

use App\Domain\Common\ValueObject\{Annee, Id, Orientation};
use App\Domain\Enveloppe\Data\BaieData;
use App\Domain\Enveloppe\Enum\{Mitoyennete, TypeParoi, TypePose};
use App\Domain\Enveloppe\Entity\Baie\DoubleFenetre;
use App\Domain\Enveloppe\Entity\Baie\{MasqueLointain, MasqueLointainCollection};
use App\Domain\Enveloppe\Entity\Baie\{MasqueProche, MasqueProcheCollection};
use App\Domain\Enveloppe\Enum\Baie\{Materiau, TypeBaie, TypeFermeture};
use App\Domain\Enveloppe\Enveloppe;
use App\Domain\Enveloppe\ValueObject\Baie\{Composition, Menuiserie, Performance, Position, Vitrage};

final class Baie extends Paroi
{
    public function __construct(
        protected readonly Id $id,
        protected readonly Enveloppe $enveloppe,
        private string $description,
        private bool $presence_protection_solaire,
        private TypeFermeture $type_fermeture,
        private ?Annee $annee_installation,
        private Composition $composition,
        private Performance $performance,
        private Position $position,
        private ?DoubleFenetre $double_fenetre,
        private MasqueProcheCollection $masques_proches,
        private MasqueLointainCollection $masques_lointains,
        private BaieData $data,
    ) {}

    public static function create(
        Id $id,
        Enveloppe $enveloppe,
        string $description,
        bool $presence_protection_solaire,
        TypeFermeture $type_fermeture,
        ?Annee $annee_installation,
        Composition $composition,
        Performance $performance,
        Position $position,
        ?DoubleFenetre $double_fenetre,
    ): self {
        return new self(
            id: $id,
            enveloppe: $enveloppe,
            description: $description,
            presence_protection_solaire: $presence_protection_solaire,
            type_fermeture: $type_fermeture,
            annee_installation: $annee_installation,
            composition: $composition,
            performance: $performance,
            position: $position,
            double_fenetre: $double_fenetre,
            masques_proches: new MasqueProcheCollection(),
            masques_lointains: new MasqueLointainCollection(),
            data: BaieData::create(),
        );
    }

    public function reinitialise(): self
    {
        $this->data = BaieData::create();
        $this->double_fenetre?->reinitialise();
        return $this;
    }

    public function calcule(BaieData $data): self
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
        return TypeParoi::PORTE;
    }

    /** @inheritdoc */
    public function paroi(): ?Paroi
    {
        return $this->position->paroi;
    }

    /** @inheritdoc */
    public function local_non_chauffe(): ?Lnc
    {
        return $this->position->local_non_chauffe;
    }

    /** @inheritdoc */
    public function mitoyennete(): Mitoyennete
    {
        return $this->position->mitoyennete;
    }

    /** @inheritdoc */
    public function orientation(): ?Orientation
    {
        return $this->position->orientation;
    }

    /** @inheritdoc */
    public function surface(): float
    {
        return $this->position->surface;
    }

    /** @inheritdoc */
    public function pont_thermique_negligeable(): bool
    {
        return $this->composition->type_baie->pont_thermique_negligeable();
    }

    public function description(): string
    {
        return $this->description;
    }

    public function presence_protection_solaire(): bool
    {
        return $this->presence_protection_solaire;
    }

    public function type_fermeture(): TypeFermeture
    {
        return $this->type_fermeture;
    }

    public function annee_installation(): ?Annee
    {
        return $this->annee_installation;
    }

    public function composition(): Composition
    {
        return $this->composition;
    }

    public function type_baie(): TypeBaie
    {
        return $this->composition->type_baie;
    }

    public function type_pose(): ?TypePose
    {
        return $this->composition->type_pose;
    }

    public function materiau(): ?Materiau
    {
        return $this->composition->materiau;
    }

    public function presence_soubassement(): ?bool
    {
        return $this->composition->presence_soubassement;
    }

    public function vitrage(): ?Vitrage
    {
        return $this->composition->vitrage;
    }

    public function menuiserie(): ?Menuiserie
    {
        return $this->composition->menuiserie;
    }

    public function performance(): Performance
    {
        return $this->performance;
    }

    public function position(): Position
    {
        return $this->position;
    }

    public function double_fenetre(): ?DoubleFenetre
    {
        return $this->double_fenetre;
    }

    public function presence_retour_isolation(): ?bool
    {
        return $this->menuiserie()->presence_retour_isolation;
    }

    public function largeur_dormant(): ?float
    {
        return $this->menuiserie()->largeur_dormant;
    }

    /**
     * @return MasqueProcheCollection|MasqueProche[]
     */
    public function masques_proches(): MasqueProcheCollection
    {
        return $this->masques_proches;
    }

    public function add_masque_proche(MasqueProche $entity): self
    {
        $this->masques_proches->add($entity);
        $this->reinitialise();
        return $this;
    }

    /**
     * @return MasqueLointainCollection|MasqueLointain[]
     */
    public function masques_lointains(): MasqueLointainCollection
    {
        return $this->masques_lointains;
    }

    public function add_masque_lointain(MasqueLointain $entity): self
    {
        $this->masques_lointains->add($entity);
        $this->reinitialise();
        return $this;
    }

    public function data(): BaieData
    {
        return $this->data;
    }
}
