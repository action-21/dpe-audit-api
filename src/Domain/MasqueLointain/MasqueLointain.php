<?php

namespace App\Domain\MasqueLointain;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Enveloppe\Enveloppe;
use App\Domain\MasqueLointain\Enum\{SecteurOrientation, TypeMasqueLointain};
use App\Domain\MasqueLointain\ValueObject\{HauteurAlpha, OrientationMasque};

/**
 * Obstacle d'environnement lointain
 */
final class MasqueLointain
{
    public function __construct(
        private readonly Id $id,
        private readonly Enveloppe $enveloppe,
        private string $description,
        private TypeMasqueLointain $type_masque,
        private HauteurAlpha $hauteur_alpha,
        private OrientationMasque $orientation,
        private ?SecteurOrientation $secteur_orientation,
    ) {
    }

    public static function create_masque_lointain_homogene(
        Enveloppe $enveloppe,
        string $description,
        HauteurAlpha $hauteur_alpha,
        OrientationMasque $orientation,
    ): self {
        return new self(
            id: Id::create(),
            enveloppe: $enveloppe,
            description: $description,
            type_masque: TypeMasqueLointain::MASQUE_LOINTAIN_HOMOGENE,
            hauteur_alpha: $hauteur_alpha,
            orientation: $orientation,
            secteur_orientation: null,
        );
    }

    public static function create_masque_lointain_non_homogene(
        Enveloppe $enveloppe,
        string $description,
        HauteurAlpha $hauteur_alpha,
        OrientationMasque $orientation,
        SecteurOrientation $secteur_orientation,
    ): self {
        return new self(
            id: Id::create(),
            enveloppe: $enveloppe,
            description: $description,
            type_masque: TypeMasqueLointain::MASQUE_LOINTAIN_NON_HOMOGENE,
            hauteur_alpha: $hauteur_alpha,
            orientation: $orientation,
            secteur_orientation: $secteur_orientation,
        );
    }

    public function update(string $description, HauteurAlpha $hauteur_alpha, OrientationMasque $orientation): self
    {
        $this->description = $description;
        $this->hauteur_alpha = $hauteur_alpha;
        $this->orientation = $orientation;
        return $this;
    }

    public function set_masque_lointain_homogene(): self
    {
        $this->secteur_orientation = null;
        $this->type_masque = TypeMasqueLointain::MASQUE_LOINTAIN_HOMOGENE;
        return $this;
    }

    public function set_masque_lointain_non_homogene(SecteurOrientation $secteur_orientation): self
    {
        $this->secteur_orientation = $secteur_orientation;
        $this->type_masque = TypeMasqueLointain::MASQUE_LOINTAIN_NON_HOMOGENE;
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

    public function description(): string
    {
        return $this->description;
    }

    public function hauteur_alpha(): HauteurAlpha
    {
        return $this->hauteur_alpha;
    }

    public function orientation(): OrientationMasque
    {
        return $this->orientation;
    }

    public function type_masque(): TypeMasqueLointain
    {
        return $this->type_masque;
    }

    public function secteur_orientation(): ?SecteurOrientation
    {
        return $this->secteur_orientation;
    }
}
