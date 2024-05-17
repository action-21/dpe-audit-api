<?php

namespace App\Domain\Lnc\Entity;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Lnc\Lnc;
use App\Domain\Lnc\Enum\{NatureMenuiserie, TypeVitrage};
use App\Domain\Lnc\ValueObject\{InclinaisonVitrage, OrientationBaie, SurfaceParoi};

/**
 * Baie vitrée du local non chauffé donnant sur l'extérieur ou en contact avec le sol (paroi enterrée, terre-plein)
 * 
 * @see https://gitlab.com/observatoire-dpe/observatoire-dpe/-/issues/118
 */
final class Baie
{
    public function __construct(
        private readonly Id $id,
        private readonly Lnc $local_non_chauffe,
        private string $description,
        private SurfaceParoi $surface,
        private InclinaisonVitrage $inclinaison_vitrage,
        private NatureMenuiserie $nature_menuiserie,
        private ?OrientationBaie $orientation,
        private ?TypeVitrage $type_vitrage,
    ) {
    }

    public static function create(
        Lnc $local_non_chauffe,
        string $description,
        SurfaceParoi $surface,
        InclinaisonVitrage $inclinaison_vitrage,
        NatureMenuiserie $nature_menuiserie,
        ?TypeVitrage $type_vitrage,
        ?OrientationBaie $orientation,
    ): self {
        $entity = new self(
            id: Id::create(),
            local_non_chauffe: $local_non_chauffe,
            description: $description,
            surface: $surface,
            inclinaison_vitrage: $inclinaison_vitrage,
            nature_menuiserie: $nature_menuiserie,
            orientation: $orientation,
            type_vitrage: $type_vitrage,
        );
        $entity->controle_coherence();
        return $entity;
    }

    public function update(
        string $description,
        SurfaceParoi $surface,
        InclinaisonVitrage $inclinaison_vitrage,
        NatureMenuiserie $nature_menuiserie,
        ?TypeVitrage $type_vitrage,
        ?OrientationBaie $orientation,
    ): self {
        $this->description = $description;
        $this->surface = $surface;
        $this->inclinaison_vitrage = $inclinaison_vitrage;
        $this->nature_menuiserie = $nature_menuiserie;
        $this->type_vitrage = $type_vitrage;
        $this->orientation = $inclinaison_vitrage->valeur() === 0 ? null : $orientation;
        $this->controle_coherence();
        return $this;
    }

    public function controle_coherence(): void
    {
        if (null === $this->type_vitrage && $this->nature_menuiserie->type_vitrage_requis()) {
            throw new \DomainException('Le type de vitrage est obligatoire pour cette nature de menuiserie');
        }
        if (0 < $this->inclinaison_vitrage->valeur() && null === $this->orientation) {
            throw new \DomainException('L\'orientation de la baie est obligatoire si l\'inclinaison du vitrage est positive');
        }
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

    public function surface(): SurfaceParoi
    {
        return $this->surface;
    }

    public function orientation(): ?OrientationBaie
    {
        return $this->orientation;
    }

    public function nature_menuiserie(): NatureMenuiserie
    {
        return $this->nature_menuiserie;
    }

    public function inclinaison_vitrage(): InclinaisonVitrage
    {
        return $this->inclinaison_vitrage;
    }

    public function type_vitrage(): ?TypeVitrage
    {
        return $this->type_vitrage;
    }
}
