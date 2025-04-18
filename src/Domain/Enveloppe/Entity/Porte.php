<?php

namespace App\Domain\Enveloppe\Entity;

use App\Domain\Common\ValueObject\{Annee, Id, Orientation};
use App\Domain\Enveloppe\Data\PorteData;
use App\Domain\Enveloppe\Enveloppe;
use App\Domain\Enveloppe\Enum\{EtatIsolation, Mitoyennete, TypeParoi, TypePose};
use App\Domain\Enveloppe\Enum\Porte\Materiau;
use App\Domain\Enveloppe\ValueObject\Porte\{Menuiserie, Position, Vitrage};

final class Porte extends Paroi
{
    public function __construct(
        private readonly Id $id,
        private readonly Enveloppe $enveloppe,
        private string $description,
        private TypePose $type_pose,
        private ?EtatIsolation $isolation,
        private ?Materiau $materiau,
        private bool $presence_sas,
        private ?Annee $annee_installation,
        private ?float $u,
        private Position $position,
        private Menuiserie $menuiserie,
        private Vitrage $vitrage,
        private PorteData $data,
    ) {}

    public static function create(
        Id $id,
        Enveloppe $enveloppe,
        string $description,
        TypePose $type_pose,
        ?EtatIsolation $isolation,
        ?Materiau $materiau,
        bool $presence_sas,
        ?Annee $annee_installation,
        ?float $u,
        Position $position,
        Menuiserie $menuiserie,
        Vitrage $vitrage,
    ): self {
        return new self(
            id: $id,
            enveloppe: $enveloppe,
            description: $description,
            type_pose: $type_pose,
            isolation: $isolation,
            materiau: $materiau,
            presence_sas: $presence_sas,
            annee_installation: $annee_installation,
            u: $u,
            position: $position,
            menuiserie: $menuiserie,
            vitrage: $vitrage,
            data: PorteData::create(),
        );
    }

    public function reinitialise(): self
    {
        $this->data = PorteData::create();
        return $this;
    }

    public function calcule(PorteData $data): self
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
        return false;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function type_pose(): TypePose
    {
        return $this->type_pose;
    }

    public function isolation(): ?EtatIsolation
    {
        return $this->isolation;
    }

    public function materiau(): ?Materiau
    {
        return $this->materiau;
    }

    public function presence_sas(): bool
    {
        return $this->presence_sas;
    }

    public function annee_installation(): ?Annee
    {
        return $this->annee_installation;
    }

    public function u(): ?float
    {
        return $this->u;
    }

    public function position(): Position
    {
        return $this->position;
    }

    public function menuiserie(): Menuiserie
    {
        return $this->menuiserie;
    }

    public function vitrage(): Vitrage
    {
        return $this->vitrage;
    }

    public function presence_retour_isolation(): ?bool
    {
        return $this->menuiserie->presence_retour_isolation;
    }

    public function largeur_dormant(): ?float
    {
        return $this->menuiserie()->largeur_dormant;
    }

    public function data(): PorteData
    {
        return $this->data;
    }
}
