<?php

namespace App\Engine\Performance\Ecs\Consommation;

use App\Domain\Audit\Audit;
use App\Domain\Common\Enum\{Energie, Mois, ScenarioUsage, TypePerte, Usage};
use App\Domain\Common\ValueObject\Consommations;
use App\Domain\Ecs\Entity\Systeme;
use App\Domain\Ecs\Enum\BouclageReseau;
use App\Engine\Performance\Ecs\Besoin\BesoinEcs;
use App\Engine\Performance\Ecs\Dimensionnement\{DimensionnementAuxiliaire, DimensionnementGenerateur, DimensionnementInstallation, DimensionnementSysteme};
use App\Engine\Performance\Ecs\Rendement\{RendementInstallation, RendementSysteme};
use App\Engine\Performance\Rule;

final class ConsommationAuxiliaire extends Rule
{
    private Systeme $systeme;

    /**
     * @see \App\Engine\Performance\Ecs\Besoin\BesoinEcs::becs()
     */
    public function becs(ScenarioUsage $scenario): float
    {
        return $this->systeme->ecs()->data()->besoins->get($scenario);
    }

    /**
     * @see \App\Engine\Performance\Ecs\Perte\PerteDistribution::pertes_distribution()
     */
    public function pertes_distribution(ScenarioUsage $scenario): float
    {
        return $this->systeme->data()->pertes->get(
            scenario: $scenario,
            type: TypePerte::DISTRIBUTION
        );
    }

    /**
     * @see \App\Engine\Performance\Ecs\Dimensionnement\DimensionnementSysteme::rdim()
     * @see \App\Engine\Performance\Ecs\Dimensionnement\DimensionnementInstallation::rdim()
     */
    public function rdim(): float
    {
        return $this->systeme->data()->rdim * $this->systeme->installation()->data()->rdim;
    }

    /**
     * @see \App\Engine\Performance\Ecs\Dimensionnement\DimensionnementAuxiliaire::paux()
     */
    public function paux(): float
    {
        return $this->systeme->generateur()->data()->paux;
    }

    /**
     * @see \App\Engine\Performance\Ecs\Dimensionnement\DimensionnementGenerateur::pn()
     */
    public function pn(): float
    {
        return $this->systeme->generateur()->data()->pn;
    }

    /**
     * Consommation annuelle des auxiliaires exprimée en kWh
     */
    public function caux(ScenarioUsage $scenario): float
    {
        return array_sum([
            $this->caux_generation($scenario),
            $this->caux_circulateur($scenario),
            $this->caux_traceur($scenario),
        ]);
    }

    /**
     * Consommation annuelle des auxiliaires de génération exprimée en kWh
     */
    public function caux_generation(ScenarioUsage $scenario): float
    {
        $becs = $this->becs($scenario);
        $rdim = $this->rdim();
        $paux = $this->paux();
        $pn = $this->pn();

        return ($paux * $becs * $rdim) / $pn / 1000;
    }

    /**
     * Consommation annuelle du circulateur exprimée en kWh
     */
    public function caux_circulateur(ScenarioUsage $scenario): float
    {
        if ($this->systeme->reseau()->bouclage === BouclageReseau::RESEAU_NON_BOUCLE) {
            return 0;
        }

        $nh = Mois::reduce(fn($carry, Mois $mois) => $carry += $mois->nh());
        $nh_puisage = $this->nh_puisage();
        $puissance_circulateur = $this->puissance_circulateur($scenario);
        $rdim = $this->rdim();

        return $nh_puisage * $puissance_circulateur + ($nh - $nh_puisage) * 20 * $rdim / 1000;
    }

    /**
     * Consommation annuelle du traceur exprimée en kWh
     */
    public function caux_traceur(ScenarioUsage $scenario): float
    {
        return $this->systeme->reseau()->bouclage === BouclageReseau::RESEAU_TRACE
            ? 0.14 * $this->becs($scenario) * $this->rdim()
            : 0;
    }

    /**
     * Puissance hydraulique de bouclage exprimée en W
     */
    public function puissance_hydraulique(ScenarioUsage $scenario): float
    {
        $pertes_distribution = $this->pertes_distribution($scenario);
        $nh_puisage = $this->nh_puisage();
        $pertes_charge_bouclage = $this->pertes_charge_bouclage();

        return $pertes_distribution / (5.815 * $nh_puisage) * $pertes_charge_bouclage / 3.6;
    }

    /**
     * Puissance électrique du circulateur exprimée en W
     */
    public function puissance_circulateur(ScenarioUsage $scenario): float
    {
        $puissance_hydraulique = $this->puissance_hydraulique($scenario);
        $efficacite_circulateur = $this->efficacite_circulateur($scenario);

        return \max(20, $puissance_hydraulique / $efficacite_circulateur);
    }

    /**
     * Efficacité du circulateur
     */
    public function efficacite_circulateur(ScenarioUsage $scenario): float
    {
        return \pow($this->puissance_hydraulique($scenario), 0.324) / 15.3;
    }

    /**
     * Nombre d'heures de puisage annuel
     */
    public function nh_puisage(): float
    {
        return Mois::reduce(fn($carry, Mois $mois) => $carry += $mois->nj() * 5);
    }

    /**
     * Longueur du bouclage exprimée en m
     */
    public function longueur_bouclage(): float
    {
        $surface = $this->systeme->installation()->surface();
        $niveaux = $this->systeme->reseau()->niveaux_desservis;
        return 4 * sqrt($surface / $niveaux) + 6 * ($niveaux - 0.5);
    }

    /**
     * Pertes de charge du bouclage de l'installation exprimées en kPa
     */
    public function pertes_charge_bouclage(): float
    {
        return 0.2 * $this->longueur_bouclage() * 10;
    }

    public function apply(Audit $entity): void
    {
        if (0 === $entity->ecs()->systemes()->count()) {
            $entity->ecs()->calcule($entity->ecs()->data()->with(
                consommations: Consommations::from()
            ));
        }
        foreach ($entity->ecs()->systemes() as $systeme) {
            $this->systeme = $systeme;

            $consommations = Consommations::create(
                usage: Usage::AUXILIAIRE,
                energie: Energie::ELECTRICITE,
                callback: fn(ScenarioUsage $scenario) => $this->caux($scenario),
            );

            $systeme->calcule($systeme->data()->with(
                consommations: $consommations,
            ));
            $systeme->installation()->calcule($systeme->installation()->data()->with(
                consommations: $consommations,
            ));
            $systeme->ecs()->calcule($systeme->ecs()->data()->with(
                consommations: $consommations,
            ));
            $entity->calcule($entity->data()->with(
                consommations: $consommations,
            ));
        }
    }

    public static function dependencies(): array
    {
        return [
            BesoinEcs::class,
            DimensionnementAuxiliaire::class,
            DimensionnementGenerateur::class,
            DimensionnementSysteme::class,
            DimensionnementInstallation::class,
            RendementSysteme::class,
            RendementInstallation::class,
        ];
    }
}
