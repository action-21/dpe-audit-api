<?php

namespace App\Domain\Batiment\Entity;

use App\Domain\Batiment\Batiment;
use App\Domain\Batiment\ValueObject\{Hauteur, SurfaceHabitable};
use App\Domain\Common\ValueObject\Id;

/**
 * Niveau de logements
 */
final class Niveau
{
    public function __construct(
        private readonly Id $id,
        private readonly Batiment $batiment,
        private SurfaceHabitable $surface_habitable,
        private Hauteur $hauteur_sous_plafond,
    ) {
    }

    public static function create(Batiment $batiment, SurfaceHabitable $surface_habitable, Hauteur $hauteur_sous_plafond): self
    {
        return new self(
            id: Id::create(),
            batiment: $batiment,
            surface_habitable: $surface_habitable,
            hauteur_sous_plafond: $hauteur_sous_plafond,
        );
    }

    public function update(SurfaceHabitable $surface_habitable, Hauteur $hauteur_sous_plafond): self
    {
        $this->surface_habitable = $surface_habitable;
        $this->hauteur_sous_plafond = $hauteur_sous_plafond;
        return $this;
    }

    public function id(): Id
    {
        return $this->id;
    }

    public function batiment(): Batiment
    {
        return $this->batiment;
    }

    public function surface_habitable(): SurfaceHabitable
    {
        return $this->surface_habitable;
    }

    public function hauteur_sous_plafond(): Hauteur
    {
        return $this->hauteur_sous_plafond;
    }
}
