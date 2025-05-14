<?php

namespace App\Engine\Performance\Chauffage\Dimensionnement;

use App\Domain\Chauffage\Entity\Systeme;
use App\Domain\Chauffage\Enum\{ConfigurationSysteme, TypeChauffage};

final class DimensionnementSystemeReleve extends DimensionnementSysteme
{
    /**
     * Ratio de dimensionnement des systèmes d'appoint
     * 
     * @see \App\Engine\Performance\Chauffage\Dimensionnement\DimensionnementSystemeAppoint::rdim()
     */
    public function rdim_appoint(): float
    {
        return $this->systeme->installation()->systemes()
            ->with_configuration(ConfigurationSysteme::APPOINT)
            ->reduce(fn(float $rdim, Systeme $item) => $rdim + $item->data()->rdim);
    }

    /**
     * Présence d'un système centrale PAC
     */
    public function has_systeme_central_pac(): float
    {
        return $this->systeme->installation()->systemes()
            ->with_type(TypeChauffage::CHAUFFAGE_CENTRAL)
            ->has_pac();
    }

    public function rdim(): float
    {
        $rdim = $this->has_systeme_central_pac() ? 0.2 : 0.25;

        return $this->systeme->installation()->systemes()->has_systeme_central_collectif()
            ? $rdim
            : $rdim * (1 - $this->rdim_appoint());
    }

    public static function match(Systeme $systeme): bool
    {
        return $systeme->data()->configuration === ConfigurationSysteme::RELEVE;
    }

    public static function dependencies(): array
    {
        return parent::dependencies() + [DimensionnementSystemeAppoint::class];
    }
}
