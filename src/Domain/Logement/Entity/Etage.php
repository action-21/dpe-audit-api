<?php

namespace App\Domain\Logement\Entity;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Logement\Logement;
use App\Domain\Logement\ValueObject\{HauteurSousPlafond, SurfaceHabitable};

/**
 * Un étage d'un logement
 */
final class Etage
{
    public function __construct(
        private readonly Id $id,
        private readonly Logement $logement,
        private string $description,
        private SurfaceHabitable $surface_habitable,
        private HauteurSousPlafond $hauteur_sous_plafond,
    ) {
    }

    public static function create(
        Logement $logement,
        string $description,
        SurfaceHabitable $surface_habitable,
        HauteurSousPlafond $hauteur_sous_plafond,
    ): self {
        return new self(
            id: Id::create(),
            logement: $logement,
            description: $description,
            surface_habitable: $surface_habitable,
            hauteur_sous_plafond: $hauteur_sous_plafond,
        );
    }

    public function update(string $description, SurfaceHabitable $surface_habitable, HauteurSousPlafond $hauteur_sous_plafond): self
    {
        $this->description = $description;
        $this->surface_habitable = $surface_habitable;
        $this->hauteur_sous_plafond = $hauteur_sous_plafond;
        return $this;
    }

    public function id(): Id
    {
        return $this->id;
    }

    public function logement(): Logement
    {
        return $this->logement;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function surface_habitable(): SurfaceHabitable
    {
        return $this->surface_habitable;
    }

    public function hauteur_sous_plafond(): HauteurSousPlafond
    {
        return $this->hauteur_sous_plafond;
    }
}
