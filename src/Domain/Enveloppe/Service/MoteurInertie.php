<?php

namespace App\Domain\Enveloppe\Service;

use App\Domain\Enveloppe\Enum\Inertie as InertieEnum;
use App\Domain\Enveloppe\Enveloppe;
use App\Domain\Enveloppe\ValueObject\Inertie;

final class MoteurInertie
{
    public function calcule_inertie(Enveloppe $entity): Inertie
    {
        $surface_paroi_verticale = $entity->parois()->murs()->surface();
        $surface_paroi_verticale += $entity->refends()->surface();
        $surface_plancher_bas = $entity->parois()->planchers_bas()->surface();
        $surface_plancher_bas += $entity->planchers_intermediaires()->surface() * 0.5;
        $surface_plancher_haut = $entity->parois()->planchers_hauts()->surface();
        $surface_plancher_haut += $entity->planchers_intermediaires()->surface() * 0.5;

        $surface_paroi_verticale_lourde = $entity->parois()->murs()->filter_by_inertie(true)->surface();
        $surface_paroi_verticale_lourde += $entity->refends()->filter_by_inertie(true)->surface();
        $surface_plancher_bas_lourd = $entity->parois()->planchers_bas()->filter_by_inertie(true)->surface();
        $surface_plancher_bas_lourd += $entity->planchers_intermediaires()->filter_by_inertie(true)->surface() * 0.5;
        $surface_plancher_haut_lourd = $entity->parois()->planchers_hauts()->filter_by_inertie(true)->surface();
        $surface_plancher_haut_lourd += $entity->planchers_intermediaires()->filter_by_inertie(true)->surface() * 0.5;

        $paroi_ancienne = $entity->parois()->murs()->filter_by_paroi_ancienne(true)->surface_deperditive()
            > $entity->parois()->murs()->surface_deperditive() / 2;

        return Inertie::create(
            inertie: $this->inertie(
                plancher_bas_lourds: $surface_plancher_bas_lourd > $surface_plancher_bas / 2,
                plancher_hauts_lourds: $surface_plancher_haut_lourd > $surface_plancher_haut / 2,
                parois_verticales_lourdes: $surface_paroi_verticale_lourde > $surface_paroi_verticale / 2,
            ),
            paroi_ancienne: $paroi_ancienne,
        );
    }

    public function inertie(bool $plancher_bas_lourds, bool $plancher_hauts_lourds, bool $parois_verticales_lourdes): InertieEnum
    {
        return InertieEnum::from_inertie_parois(
            inertie_planchers_bas: $plancher_bas_lourds,
            inertie_planchers_hauts: $plancher_hauts_lourds,
            inertie_parois_verticales: $parois_verticales_lourdes,
        );
    }
}
