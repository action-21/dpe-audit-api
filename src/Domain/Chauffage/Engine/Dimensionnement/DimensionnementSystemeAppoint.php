<?php

namespace App\Domain\Chauffage\Engine\Dimensionnement;

use App\Domain\Chauffage\Entity\Systeme;
use App\Domain\Chauffage\Enum\ConfigurationSysteme;

final class DimensionnementSystemeAppoint extends DimensionnementSysteme
{
    /**
     * Somme des puissances nominales des systèmes d'appoint exprimée en kW
     */
    public function pn_appoint(): ?float
    {
        $systemes = $this->systeme->installation()->systemes()
            ->with_configuration(ConfigurationSysteme::APPOINT);
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
     * Nombre de systèmes d'appoint
     */
    public function n_appoint(): int
    {
        return $this->systeme->installation()->systemes()
            ->with_configuration(ConfigurationSysteme::APPOINT)
            ->count();
    }

    /**
     * Dans le cas d'un système de chauffage central collectif, le dimens
     */
    public function rdim(): float
    {
        $rdim = $this->systeme->installation()->systemes()->has_systeme_central_collectif() ? 1 : 0.25;
        return ($pn_appoint = $this->pn_appoint())
            ? $rdim * ($this->systeme->generateur()->signaletique()->pn / $pn_appoint)
            : $rdim * (1 / $this->n_appoint());
    }

    public static function supports(Systeme $systeme): bool
    {
        return $systeme->data()->configuration === ConfigurationSysteme::APPOINT;
    }
}
