<?php

namespace App\Engine\Performance\Deperdition;

use App\Domain\Audit\Audit;
use App\Domain\Enveloppe\Entity\Lnc\Baie;
use App\Domain\Enveloppe\Enum\EtatIsolation;
use App\Domain\Enveloppe\Enum\Lnc\TypeVitrage;
use App\Domain\Enveloppe\Enum\Mitoyennete;
use App\Engine\Performance\Rule;

final class DeperditionBaieLnc extends Rule
{
    private Baie $baie;

    /**
     * Détermination de la surface donnant sur l'extérieur
     */
    public function aue(): float
    {
        return \in_array($this->baie->position()->mitoyennete, [
            Mitoyennete::EXTERIEUR,
            Mitoyennete::ENTERRE,
            Mitoyennete::VIDE_SANITAIRE,
            Mitoyennete::TERRE_PLEIN,
        ]) ? $this->baie->position()->surface : 0;
    }

    /**
     * Détermination de la surface donnant sur un local chauffé
     */
    public function aiu(): float
    {
        return $this->baie->position()->mitoyennete === Mitoyennete::LOCAL_RESIDENTIEL
            ? $this->baie->position()->surface
            : 0;
    }

    /**
     * Etat d'isolation de la baie
     */
    public function isolation(): EtatIsolation
    {
        if ($this->baie->type()->is_paroi_vitree()) {
            return EtatIsolation::NON_ISOLE;
        }
        return match ($this->baie->type_vitrage()) {
            TypeVitrage::TRIPLE_VITRAGE, TypeVitrage::TRIPLE_VITRAGE_FE => EtatIsolation::ISOLE,
            default => EtatIsolation::NON_ISOLE,
        };
    }

    public function apply(Audit $entity): void
    {
        foreach ($entity->enveloppe()->locaux_non_chauffes() as $lnc) {
            foreach ($lnc->baies() as $baie) {
                $this->baie = $baie;
                $this->clear();

                $baie->calcule($baie->data()->with(
                    aue: $this->aue(),
                    aiu: $this->aiu(),
                    isolation: $this->isolation(),
                ));
            }
        }
    }
}
