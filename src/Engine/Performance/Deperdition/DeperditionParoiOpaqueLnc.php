<?php

namespace App\Engine\Performance\Deperdition;

use App\Domain\Audit\Audit;
use App\Domain\Enveloppe\Entity\Lnc\ParoiOpaque;
use App\Domain\Enveloppe\Enum\{EtatIsolation, Mitoyennete};
use App\Engine\Performance\Rule;

final class DeperditionParoiOpaqueLnc extends Rule
{
    private ParoiOpaque $paroi;

    /**
     * Détermination de la surface donnant sur l'extérieur
     */
    public function aue(): float
    {
        return \in_array($this->paroi->position()->mitoyennete, [
            Mitoyennete::EXTERIEUR,
            Mitoyennete::ENTERRE,
            Mitoyennete::VIDE_SANITAIRE,
            Mitoyennete::TERRE_PLEIN,
        ]) ? $this->paroi->position()->surface : 0;
    }

    /**
     * Détermination de la surface donnant sur un local chauffé
     */
    public function aiu(): float
    {
        return $this->paroi->position()->mitoyennete === Mitoyennete::LOCAL_RESIDENTIEL
            ? $this->paroi->position()->surface
            : 0;
    }

    /**
     * Etat d'isolation de la paroi
     */
    public function isolation(): EtatIsolation
    {
        return $this->paroi->isolation() ?? EtatIsolation::NON_ISOLE;
    }

    public function apply(Audit $entity): void
    {
        foreach ($entity->enveloppe()->locaux_non_chauffes() as $lnc) {
            foreach ($lnc->parois_opaques() as $paroi) {
                $this->paroi = $paroi;
                $paroi->calcule($paroi->data()->with(
                    aue: $this->aue(),
                    aiu: $this->aiu(),
                    isolation: $this->isolation(),
                ));
            }
        }
    }
}
