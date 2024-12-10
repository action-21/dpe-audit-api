<?php

namespace App\Domain\Ecs\Service;

use App\Domain\Common\Enum\{Energie, Mois, ScenarioUsage, Usage};
use App\Domain\Common\ValueObject\ConsommationCollection;
use App\Domain\Ecs\Entity\Systeme;
use App\Domain\Ecs\Enum\BouclageReseau;

/**
 * @use \App\Domain\Ecs\Service\MoteurDimensionnement
 * @use \App\Domain\Ecs\Service\MoteurRendement
 */
final class MoteurConsommation
{
    public function __construct(
        private MoteurDimensionnement $moteur_dimensionnement,
    ) {}

    public function calcule_consommations(Systeme $entity): ConsommationCollection
    {
        $rdim = $this->moteur_dimensionnement->calcule_dimensionnement($entity);

        return ConsommationCollection::create(
            usage: Usage::ECS,
            energie: $entity->generateur()->signaletique()->energie->to(),
            callback: fn(ScenarioUsage $scenario): float => $this->cecs(
                becs: $entity->ecs()->besoins()->besoins(scenario: $scenario),
                iecs: $entity->rendements()->iecs(scenario: $scenario),
                fecs: $entity->rendements()->fecs(scenario: $scenario),
                rdim: $rdim,
            )
        );
    }

    public function calcule_consommations_auxiliaires(Systeme $entity): ConsommationCollection
    {
        return ConsommationCollection::fromCollections(
            $this->calcule_consommations_auxiliaire_generation($entity),
            $this->calcule_consommations_auxiliaire_distribution($entity),
        );
    }

    public function calcule_consommations_auxiliaire_generation(Systeme $entity): ConsommationCollection
    {
        if (false === $entity->generateur()->generateur_collectif())
            return new ConsommationCollection();

        $rdim = $this->moteur_dimensionnement->calcule_dimensionnement($entity);

        return ConsommationCollection::create(
            usage: Usage::AUXILIAIRE,
            energie: Energie::ELECTRICITE,
            callback: fn(ScenarioUsage $scenario) => $this->caux_generation(
                becs: $entity->ecs()->besoins()->besoins(scenario: $scenario),
                pn: $entity->generateur()->performance()->pn,
                paux: $entity->generateur()->performance()->paux,
                rdim: $rdim,
            )
        );
    }

    public function calcule_consommations_auxiliaire_distribution(Systeme $entity): ConsommationCollection
    {
        if (false === $entity->generateur()->generateur_collectif())
            return new ConsommationCollection();

        $rdim = $this->moteur_dimensionnement->calcule_dimensionnement($entity);

        return ConsommationCollection::create(
            usage: Usage::AUXILIAIRE,
            energie: Energie::ELECTRICITE,
            callback: fn(ScenarioUsage $scenario) => $this->caux_circulateur(
                bouclage: $entity->reseau()->type_bouclage,
                pertes_distribution: $entity->pertes_distribution()->pertes(scenario: $scenario),
                surface: $entity->installation()->surface(),
                niveaux: $entity->reseau()->niveaux_desservis,
                rdim: $rdim,
            ) + $this->caux_traceur(
                bouclage: $entity->reseau()->type_bouclage,
                becs: $entity->ecs()->besoins()->besoins(scenario: $scenario),
                rdim: $rdim,
            )
        );
    }

    /**
     * Consommation annuelle d'eau chaude sanitaire en kWh
     * 
     * @param float $becs - Besoin d'eau chaude sanitaire en kWh
     * @param float $iecs - Inverse du rendement d'eau chaude sanitaire
     * @param float $fecs - Facteur de couverture solaire
     * @param float $rdim - Ratio de dimensionnement
     */
    public function cecs(float $becs, float $iecs, float $fecs, float $rdim): float
    {
        return $becs * (1 - $fecs) * $iecs * $rdim;
    }

    /**
     * Consommation annuelle des auxiliaires de génération en kWh
     * 
     * @param float $becs - Besoin d'eau chaude sanitaire en kWh
     * @param float $pn - Puissance nominale du générateur en kW
     * @param float $paux - Puissance des auxiliaires degénération en W
     * @param float $rdim - Ratio de dimensionnement (installation x système)
     */
    public function caux_generation(float $becs, float $pn, float $paux, float $rdim): float
    {
        return ($paux * $becs * $rdim) / $pn / 1000;
    }

    /**
     * Consommation annuelle du circulateur en kWh
     * 
     * @param float $pertes_distribution - Pertes de distribution en Wh
     * @param float $surface - Surface couverte par l'installation en m²
     * @param int $niveaux - Nombre de niveaux désservis par l'intsllation
     * @param float $rdim - Ratio de dimensionnement (installation x système)
     */
    public function caux_circulateur(BouclageReseau $bouclage, float $pertes_distribution, float $surface, int $niveaux, float $rdim): float
    {
        if ($bouclage === BouclageReseau::RESEAU_NON_BOUCLE)
            return 0;

        $nh = Mois::reduce(fn($carry, Mois $mois) => $carry += $mois->nh());
        $longueur_bouclage = $this->longueur_bouclage(surface: $surface, niveaux: $niveaux);
        $pertes_bouclage = $this->pertes_charge_bouclage(longueur_bouclage: $longueur_bouclage);
        $puissance_hydraulique = $this->puissance_hydraulique(pertes_distribution: $pertes_distribution, pertes_charge_bouclage: $pertes_bouclage);
        $puissance_circulateur = $this->puissance_circulateur(puissance_hydraulique: $puissance_hydraulique);

        return $this->nh_puisage() * $puissance_circulateur + ($nh - $this->nh_puisage()) * 20 * $rdim / 1000;
    }

    /**
     * Consommation annuelle du traceur en kWh
     * 
     * @param float $becs - Besoin d'eau chaude sanitaire en kWh
     * @param float $rdim - Ratio de dimensionnement (installation x système)
     */
    public function caux_traceur(BouclageReseau $bouclage, float $becs, float $rdim): float
    {
        return $bouclage === BouclageReseau::RESEAU_TRACE ? 0.14 * $becs * $rdim : 0;
    }

    /**
     * Puissance hydraulique de bouclage en W
     * 
     * @param float $pertes_distribution - Pertes de distribution en Wh
     * @param float $pertes_charge_bouclage - Pertes de charge du bouclage en kPa
     */
    public function puissance_hydraulique(float $pertes_distribution, float $pertes_charge_bouclage): float
    {
        return $pertes_distribution / (5.815 * $this->nh_puisage()) * $pertes_charge_bouclage / 3.6;
    }

    /**
     * Puissance électrique du circulateur en W
     * 
     * @param float $puissance_hydraulique - Puissance hydraulique de bouclage en W
     */
    public function puissance_circulateur(float $puissance_hydraulique): float
    {
        $efficacite_circulateur = $this->efficacite_circulateur($puissance_hydraulique);
        return \max(20, $puissance_hydraulique / $efficacite_circulateur);
    }

    /**
     * Efficacité du circulateur
     * 
     * @param float $puissance_hydraulique - Puissance hydraulique de bouclage en W
     */
    public function efficacite_circulateur(float $puissance_hydraulique): float
    {
        return \pow($puissance_hydraulique, 0.324) / 15.3;
    }

    /**
     * Nombre d'heures de puisage annuel
     */
    public function nh_puisage(): float
    {
        return Mois::reduce(fn($carry, Mois $mois) => $carry += $mois->nj() * 5);
    }

    /**
     * Longueur du bouclage en m
     * 
     * @param float $surface - Surface couverte par l'installation en m²
     * @param int $niveaux - Nombre de niveaux désservis par l'intsllation
     */
    public function longueur_bouclage(float $surface, int $niveaux): float
    {
        return 4 * sqrt($surface / $niveaux) + 6 * ($niveaux - 0.5);
    }

    /**
     * Pertes de charge du bouclage de l'installation en kPa
     * 
     * @param float $longueur_bouclage - Longueur du bouclage en m
     */
    public function pertes_charge_bouclage(float $longueur_bouclage): float
    {
        return 0.2 * $longueur_bouclage * 10;
    }
}
