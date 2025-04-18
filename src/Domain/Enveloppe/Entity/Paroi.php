<?php

namespace App\Domain\Enveloppe\Entity;

use App\Domain\Common\ValueObject\{Id, Orientation};
use App\Domain\Enveloppe\Enum\{Mitoyennete, TypeParoi};
use App\Domain\Enveloppe\Enveloppe;
use App\Domain\Enveloppe\Entity\Lnc;

abstract class Paroi
{
    abstract public function id(): Id;

    abstract public function enveloppe(): Enveloppe;

    /**
     * Type de paroi
     */
    abstract public function type_paroi(): TypeParoi;

    /**
     * Local non chauffé associé
     */
    abstract public function local_non_chauffe(): ?Lnc;

    /**
     * Paroi associée
     */
    abstract public function paroi(): ?Paroi;

    /**
     * Mitoyenneté de la paroi
     */
    abstract public function mitoyennete(): Mitoyennete;

    /**
     * Orientation de la paroi
     */
    abstract public function orientation(): ?Orientation;

    /**
     * Non prise en compte des ponts thermiques
     */
    abstract public function pont_thermique_negligeable(): bool;

    /**
     * Surface de la paroi en m²
     */
    abstract public function surface(): float;

    /**
     * Surface de référence de la paroi en m²
     */
    public function surface_reference(): float
    {
        $surface = $this->surface();

        foreach (TypeParoi::cases() as $type_paroi) {
            $surface -= $this->enveloppe()
                ->parois($type_paroi)
                ->with_paroi(id: $this->id())
                ->surface_reference();
        }
        return $surface;
    }
}
