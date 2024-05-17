<?php

namespace App\Domain\PontThermique;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Enveloppe\Enveloppe;
use App\Domain\PontThermique\Enum\TypeLiaison;
use App\Domain\PontThermique\ValueObject\{Kpt, Longueur};

final class PontThermique
{
    public function __construct(
        private readonly Id $id,
        private readonly Enveloppe $enveloppe,
        private TypeLiaison $type_liaison,
        private string $description,
        private bool $pont_thermique_partiel,
        private Longueur $longueur,
        private ?Kpt $valeur,
        private ?Id $mur_id = null,
        private ?Id $plancher_id = null,
        private ?Id $refend_id = null,
        private ?Id $ouverture_id = null,
    ) {
    }

    public function update(string $description, bool $pont_thermique_partiel, Longueur $longueur, ?Kpt $kpt): self
    {
        $this->description = $description;
        $this->pont_thermique_partiel = $pont_thermique_partiel;
        $this->longueur = $longueur;
        $this->valeur = $kpt;
        return $this;
    }

    public function set_liaison_plancher_bas_mur(Id $plancher_id, Id $mur_id): self
    {
        if (null === $this->enveloppe->mur_collection()->find($mur_id)) {
            throw new \InvalidArgumentException("Mur $mur_id not found");
        }
        if (null === $this->enveloppe->plancher_haut_collection()->find($plancher_id)) {
            if (null === $this->enveloppe->plancher_bas_collection()->find($plancher_id)) {
                throw new \InvalidArgumentException("Plancher $plancher_id not found");
            }
        }
        $this->mur_id = $mur_id;
        $this->plancher_id = $plancher_id;
        $this->refend_id = null;
        $this->ouverture_id = null;
        $this->type_liaison = TypeLiaison::PLANCHER_BAS_MUR;
        return $this;
    }

    public function set_liaison_plancher_intermediaire_mur(Id $plancher_id, Id $mur_id): self
    {
        if (null === $this->enveloppe->mur_collection()->find($mur_id)) {
            throw new \InvalidArgumentException("Mur $mur_id not found");
        }
        if (null === $this->enveloppe->plancher_intermediaire_collection()->find($plancher_id)) {
            throw new \InvalidArgumentException("Plancher intermédiaire $plancher_id not found");
        }
        $this->mur_id = $mur_id;
        $this->plancher_id = $plancher_id;
        $this->refend_id = null;
        $this->ouverture_id = null;
        $this->type_liaison = TypeLiaison::PLANCHER_INTERMEDIAIRE_MUR;
        return $this;
    }

    public function set_liaison_plancher_haut_mur(Id $mur_id, Id $plancher_id): self
    {
        if (null === $this->enveloppe->mur_collection()->find($mur_id)) {
            throw new \InvalidArgumentException("Mur $mur_id not found");
        }
        if (null === $this->enveloppe->plancher_haut_collection()->find($plancher_id)) {
            throw new \InvalidArgumentException("Plancher haut $plancher_id not found");
        }
        $this->mur_id = $mur_id;
        $this->plancher_id = $plancher_id;
        $this->refend_id = null;
        $this->ouverture_id = null;
        $this->type_liaison = TypeLiaison::PLANCHER_HAUT_MUR;
        return $this;
    }

    public function set_liaison_refend_mur(Id $refend_id, Id $mur_id): self
    {
        if (null === $this->enveloppe->mur_collection()->find($mur_id)) {
            throw new \InvalidArgumentException("Mur $mur_id not found");
        }
        if (null === $this->enveloppe->refend_collection()->find($refend_id)) {
            throw new \InvalidArgumentException("Refend $refend_id not found");
        }
        $this->mur_id = $mur_id;
        $this->refend_id = $refend_id;
        $this->plancher_id = null;
        $this->ouverture_id = null;
        $this->type_liaison = TypeLiaison::REFEND_MUR;
        return $this;
    }

    public function set_liaison_menuiserie_mur(Id $ouverture_id, Id $mur_id): self
    {
        if (null === $this->enveloppe->mur_collection()->find($mur_id)) {
            throw new \InvalidArgumentException("Mur $mur_id not found");
        }
        if (null === $this->enveloppe->paroi_collection()->search_ouverture()->find($ouverture_id)) {
            throw new \InvalidArgumentException("Ouverture $ouverture_id not found");
        }
        $this->mur_id = $mur_id;
        $this->ouverture_id = $ouverture_id;
        $this->plancher_id = null;
        $this->refend_id = null;
        $this->type_liaison = TypeLiaison::MENUISERIE_MUR;
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

    public function mur_id(): ?Id
    {
        return $this->mur_id;
    }

    public function plancher_id(): ?Id
    {
        return $this->plancher_id;
    }

    public function ouverture_id(): ?Id
    {
        return $this->ouverture_id;
    }

    public function refend_id(): ?Id
    {
        return $this->refend_id;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function type_liaison(): TypeLiaison
    {
        return $this->type_liaison;
    }

    public function longueur(): Longueur
    {
        return $this->longueur;
    }

    public function valeur(): ?Kpt
    {
        return $this->valeur;
    }

    public function pont_thermique_partiel(): bool
    {
        return $this->pont_thermique_partiel;
    }
}
