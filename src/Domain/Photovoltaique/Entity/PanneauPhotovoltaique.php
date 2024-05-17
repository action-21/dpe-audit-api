<?php

namespace App\Domain\Photovoltaique\Entity;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Photovoltaique\InstallationPhotovoltaique;
use App\Domain\Photovoltaique\ValueObject\{InclinaisonCapteur, Modules, OrientationCapteur, SurfaceCapteur};

final class PanneauPhotovoltaique
{
    public function __construct(
        private readonly Id $id,
        private readonly InstallationPhotovoltaique $installation,
        private ?SurfaceCapteur $surface_capteurs,
        private ?Modules $modules,
        private ?InclinaisonCapteur $orientation,
        private ?OrientationCapteur $inclinaison,
    ) {
    }

    public function id(): Id
    {
        return $this->id;
    }

    public function installation(): InstallationPhotovoltaique
    {
        return $this->installation;
    }

    public function surface_capteurs(): ?SurfaceCapteur
    {
        return $this->surface_capteurs;
    }

    public function surface_capteurs_defaut(): float
    {
        return $this->surface_capteurs?->valeur() ?? 1.6 * $this->modules()->valeur();
    }

    public function modules(): ?Modules
    {
        return $this->modules;
    }

    public function inclinaison(): ?InclinaisonCapteur
    {
        return $this->inclinaison;
    }

    public function orientation(): ?OrientationCapteur
    {
        return $this->orientation;
    }
}
