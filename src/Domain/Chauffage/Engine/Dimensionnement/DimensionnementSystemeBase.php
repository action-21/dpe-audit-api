<?php

namespace App\Domain\Chauffage\Engine\Dimensionnement;

use App\Domain\Chauffage\Entity\Systeme;
use App\Domain\Chauffage\Enum\{ConfigurationSysteme, TypeChauffage};

final class DimensionnementSystemeBase extends DimensionnementSysteme
{
    /**
     * Ratio de dimensionnement des systèmes en relève
     * 
     * @see \App\Domain\Chauffage\Engine\Dimensionnement\DimensionnementSystemeReleve::rdim()
     */
    public function rdim_releve(): float
    {
        return $this->systeme->installation()->systemes()
            ->with_configuration(ConfigurationSysteme::RELEVE)
            ->reduce(fn(float $rdim, Systeme $item) => $rdim + $item->data()->rdim);
    }

    /**
     * Ratio de dimensionnement des systèmes d'appoint
     * 
     * @see \App\Domain\Chauffage\Engine\Dimensionnement\DimensionnementSystemeAppoint::rdim()
     */
    public function rdim_appoint(): float
    {
        return $this->systeme->installation()->systemes()
            ->with_configuration(ConfigurationSysteme::APPOINT)
            ->reduce(fn(float $rdim, Systeme $item) => $rdim + $item->data()->rdim);
    }

    /**
     * Somme des puissances nominales des systèmes en base exprimée en kW
     */
    public function pn_base(): ?float
    {
        $systemes = $this->systeme->installation()->systemes()
            ->with_configuration(ConfigurationSysteme::BASE);
        $pn = 0;

        foreach ($systemes as $systeme) {
            if (null === $systeme->generateur()->signaletique()->pn) {
                return null;
            }
            $pn += $systeme->generateur()->signaletique()->pn;
        }
        return $pn;
    }

    /**
     * Présence d'un système de chauffage central
     */
    public function has_systeme_central(): bool
    {
        return $this->systeme->installation()->systemes()
            ->with_type(TypeChauffage::CHAUFFAGE_CENTRAL)
            ->count() > 0;
    }

    /**
     * Présence d'un système en relève
     */
    public function has_releve(): bool
    {
        return $this->systeme->installation()->systemes()
            ->with_configuration(ConfigurationSysteme::RELEVE)
            ->count() > 0;
    }

    /**
     * Nombre de systèmes en base
     */
    public function n_base(): int
    {
        return $this->systeme->installation()->systemes()
            ->with_configuration(ConfigurationSysteme::BASE)
            ->count();
    }

    public function rdim(): float
    {
        if (false === $this->has_systeme_central()) {
            return ($pn_base = $this->pn_base())
                ? $this->systeme->generateur()->signaletique()->pn / $pn_base
                : 1 / $this->n_base();
        }
        return $this->systeme->installation()->systemes()->has_systeme_central_collectif()
            ? 1 * (1 - $this->rdim_releve())
            : 1 * (1 - $this->rdim_releve()) * (1 - $this->rdim_appoint());
    }

    public static function supports(Systeme $systeme): bool
    {
        return $systeme->data()->configuration === ConfigurationSysteme::BASE;
    }

    public static function dependencies(): array
    {
        return parent::dependencies() + [
            DimensionnementSystemeAppoint::class,
            DimensionnementSystemeReleve::class,
        ];
    }
}
