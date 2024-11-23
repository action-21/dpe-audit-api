<?php

namespace App\Domain\Enveloppe\Service;

use App\Domain\Lnc\Service\MoteurSurfaceDeperditive as MoteurSurfaceDeperditiveLnc;
use App\Domain\Mur\Service\MoteurSurfaceDeperditive as MoteurSurfaceDeperditiveMur;
use App\Domain\PlancherBas\Service\MoteurSurfaceDeperditive as MoteurSurfaceDeperditivePlancherBas;
use App\Domain\PlancherHaut\Service\MoteurSurfaceDeperditive as MoteurSurfaceDeperditivePlancherHaut;

/**
 * @uses \App\Domain\Lnc\Service\MoteurSurfaceDeperditive
 * @uses \App\Domain\Mur\Service\MoteurSurfaceDeperditive
 * @uses \App\Domain\PlancherBas\Service\MoteurSurfaceDeperditive
 * @uses \App\Domain\PlancherHaut\Service\MoteurSurfaceDeperditive
 */
final class MoteurSurfaceDeperditive
{
    public function __construct(
        private MoteurSurfaceDeperditiveLnc $moteur_surface_deperditive_lnc,
        private MoteurSurfaceDeperditiveMur $moteur_surface_deperditive_mur,
        private MoteurSurfaceDeperditivePlancherBas $moteur_surface_deperditive_plancher_bas,
        private MoteurSurfaceDeperditivePlancherHaut $moteur_surface_deperditive_plancher_haut,
    ) {}

    public function moteur_surface_deperditive_lnc(): MoteurSurfaceDeperditiveLnc
    {
        return $this->moteur_surface_deperditive_lnc;
    }

    public function moteur_surface_deperditive_mur(): MoteurSurfaceDeperditiveMur
    {
        return $this->moteur_surface_deperditive_mur;
    }

    public function moteur_surface_deperditive_plancher_bas(): MoteurSurfaceDeperditivePlancherBas
    {
        return $this->moteur_surface_deperditive_plancher_bas;
    }

    public function moteur_surface_deperditive_plancher_haut(): MoteurSurfaceDeperditivePlancherHaut
    {
        return $this->moteur_surface_deperditive_plancher_haut;
    }
}
