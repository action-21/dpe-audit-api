<?php

namespace App\Domain\Lnc\Service;

use App\Domain\Lnc\Entity\{Baie, Paroi};
use App\Domain\Lnc\Enum\Mitoyennete;
use App\Domain\Lnc\Lnc;

/**
 * @uses \App\Domain\Mur\Service\MoteurSurfaceDeperditive
 * @uses \App\Domain\PlancherBas\Service\MoteurSurfaceDeperditive
 * @uses \App\Domain\PlancherHaut\Service\MoteurSurfaceDeperditive
 */
final class MoteurSurfaceDeperditive
{
    public function calcule_surface_deperditive(Lnc $entity): self
    {
        $entity->parois()->calcule_surface_deperditive($this);
        $entity->baies()->calcule_surface_deperditive($this);
        return $this;
    }

    public function calcule_aue(Lnc $entity): float
    {
        return $entity->parois()->aue() + $entity->baies()->aue();
    }

    public function calcule_aiu(Lnc $entity): float
    {
        return $entity->parois()->aiu() + $entity->baies()->aiu() + $entity->enveloppe()->parois()->aiu(
            local_non_chauffe_id: $entity->id()
        );
    }

    public function calcule_isolation_aue(Lnc $entity): bool
    {
        $aue_isole = $entity->parois()->aue(isolation: true) + $entity->baies()->aue(isolation: true);
        return $aue_isole > $entity->aue() / 2;
    }

    public function calcule_isolation_aiu(Lnc $entity): bool
    {
        $aiu_isole = $entity->parois()->aiu(isolation: true) + $entity->baies()->aiu(isolation: true);
        $aiu_isole += $entity->enveloppe()->parois()->aiu(local_non_chauffe_id: $entity->id(), isolation: true);
        return $aiu_isole > $entity->aiu() / 2;
    }

    public function calcule_aue_paroi(Paroi $entity): float
    {
        if ($this->aue_applicable(mitoyennete: $entity->position()->mitoyennete())) {
            $aue = $entity->surface();
            $aue -= $entity->local_non_chauffe()->baies()->filter_by_paroi(id: $entity->id())->surface();
            return \max(0, $aue);
        }
        return 0;
    }

    public function calcule_aiu_paroi(Paroi $entity): float
    {
        if ($this->aiu_applicable(mitoyennete: $entity->position()->mitoyennete())) {
            $aue = $entity->surface();
            $aue -= $entity->local_non_chauffe()->baies()->filter_by_paroi(id: $entity->id())->surface();
            return \max(0, $aue);
        }
        return 0;
    }
    public function calcule_aue_baie(Baie $entity): float
    {
        return $this->aue_applicable(mitoyennete: $entity->position()->mitoyennete())
            ? $entity->surface()
            : 0;
    }

    public function calcule_aiu_baie(Baie $entity): float
    {
        return $this->aiu_applicable(mitoyennete: $entity->position()->mitoyennete())
            ? $entity->surface()
            : 0;
    }

    public function aue_applicable(Mitoyennete $mitoyennete): bool
    {
        return \in_array($mitoyennete, [
            Mitoyennete::EXTERIEUR,
            Mitoyennete::ENTERRE,
            Mitoyennete::VIDE_SANITAIRE,
            Mitoyennete::TERRE_PLEIN,
        ]);
    }

    public function aiu_applicable(Mitoyennete $mitoyennete): bool
    {
        return \in_array($mitoyennete, [
            Mitoyennete::LOCAL_RESIDENTIEL,
        ]);
    }
}
