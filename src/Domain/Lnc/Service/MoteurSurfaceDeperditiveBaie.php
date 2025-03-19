<?php

namespace App\Domain\Lnc\Service;

use App\Domain\Common\Service\MoteurCalcul;
use App\Domain\Lnc\Entity\Baie;
use App\Domain\Lnc\Enum\{EtatIsolation, Mitoyennete, TypeVitrage};
use App\Domain\Lnc\ValueObject\SurfaceDeperditiveParoi;

final class MoteurSurfaceDeperditiveBaie extends MoteurCalcul
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
    public function isolation(?TypeVitrage $type_vitrage,): EtatIsolation
    {
        if (null === $type_vitrage) {
            $this->valeurs_forfaitaires()->add('type_vitrage');
            return EtatIsolation::NON_ISOLE;
        }
        return $type_vitrage->isolation();
    }

    public function __invoke(Baie $entity): SurfaceDeperditiveParoi
    {
        $this->valeurs_forfaitaires()->reset();

        return SurfaceDeperditiveParoi::create(
            aue: $this->aue(mitoyennete: $entity->position()->mitoyennete, surface: $entity->position()->surface),
            aiu: $this->aiu(mitoyennete: $entity->position()->mitoyennete, surface: $entity->position()->surface),
            isolation: $this->isolation($entity->type_vitrage()),
            valeurs_forfaitaires: $this->valeurs_forfaitaires(),
        );
    }
}
