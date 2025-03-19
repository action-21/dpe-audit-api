<?php

namespace App\Domain\Lnc\Service;

use App\Domain\Common\Service\MoteurCalcul;
use App\Domain\Lnc\Entity\ParoiOpaque;
use App\Domain\Lnc\Enum\{EtatIsolation, Mitoyennete};
use App\Domain\Lnc\ValueObject\SurfaceDeperditiveParoi;

final class MoteurSurfaceDeperditiveParoiOpaque extends MoteurCalcul
{
    /**
     * Détermination de la surface donnant sur l'extérieur
     */
    public function aue(Mitoyennete $mitoyennete, float $surface,): float
    {
        return \in_array($mitoyennete, [
            Mitoyennete::EXTERIEUR,
            Mitoyennete::ENTERRE,
            Mitoyennete::VIDE_SANITAIRE,
            Mitoyennete::TERRE_PLEIN,
        ]) ? $surface : 0;
    }

    /**
     * Détermination de la surface donnant sur un local chauffé
     */
    public function aiu(Mitoyennete $mitoyennete, float $surface,): float
    {
        return $mitoyennete === Mitoyennete::LOCAL_RESIDENTIEL ? $surface : 0;
    }

    /**
     * Détermination de l'état d'isolation de la paroi
     */
    public function isolation(?EtatIsolation $isolation,): EtatIsolation
    {
        if (null === $isolation) {
            $this->valeurs_forfaitaires()->add('isolation');
            return EtatIsolation::NON_ISOLE;
        }
        return $isolation;
    }

    public function __invoke(ParoiOpaque $entity): SurfaceDeperditiveParoi
    {
        $this->valeurs_forfaitaires()->reset();

        return SurfaceDeperditiveParoi::create(
            aue: $this->aue(mitoyennete: $entity->position()->mitoyennete, surface: $entity->surface_opaque()),
            aiu: $this->aiu(mitoyennete: $entity->position()->mitoyennete, surface: $entity->surface_opaque()),
            isolation: $this->isolation($entity->isolation()),
            valeurs_forfaitaires: $this->valeurs_forfaitaires(),
        );
    }
}
