<?php

namespace App\Domain\Lnc\Service;

use App\Domain\Common\Service\MoteurCalcul;
use App\Domain\Lnc\Enum\EtatIsolation;
use App\Domain\Lnc\Lnc;
use App\Domain\Lnc\ValueObject\SurfaceDeperditive;

final class MoteurSurfaceDeperditive extends MoteurCalcul
{
    public function __construct(
        private MoteurSurfaceDeperditiveParoiOpaque $moteur_surface_deperditive_paroi_opaque,
        private MoteurSurfaceDeperditiveBaie $moteur_surface_deperditive_baie
    ) {}

    public function __invoke(Lnc $entity): SurfaceDeperditive
    {
        $this->valeurs_forfaitaires()->reset();

        $entity->baies()->calcule_surface_deperditive($this->moteur_surface_deperditive_baie);
        $entity->parois()->calcule_surface_deperditive($this->moteur_surface_deperditive_paroi_opaque);

        $aue = $entity->parois()->aue() + $entity->baies()->aue();
        $aiu = $entity->parois()->aiu() + $entity->baies()->aiu() + $entity->enveloppe()->parois()->aiu(
            local_non_chauffe_id: $entity->id()
        );

        $aue_isole = $entity->parois()->aue(isolation: true) + $entity->baies()->aue(isolation: true);
        $isolation_aue = $aue_isole > $aue / 2 ? EtatIsolation::ISOLE : EtatIsolation::NON_ISOLE;

        $aiu_isole = $entity->parois()->aiu(isolation: true) + $entity->baies()->aiu(isolation: true);
        $aiu_isole += $entity->enveloppe()->parois()->aiu(local_non_chauffe_id: $entity->id(), isolation: true);
        $isolation_aiu = $aiu_isole > $aiu / 2 ? EtatIsolation::ISOLE : EtatIsolation::NON_ISOLE;

        return SurfaceDeperditive::create(
            aue: $aue,
            aiu: $aiu,
            isolation_aue: $isolation_aue,
            isolation_aiu: $isolation_aiu,
            valeurs_forfaitaires: $this->valeurs_forfaitaires(),
        );
    }
}
