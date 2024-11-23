<?php

namespace App\Domain\Chauffage\Service\MoteurConsommation;

use App\Domain\Chauffage\Entity\{Emetteur, Installation, Systeme};
use App\Domain\Chauffage\Enum\{TemperatureDistribution, TypeEmetteur};
use App\Domain\Chauffage\Service\MoteurDimensionnement;
use App\Domain\Common\Enum\{Energie, Mois, ScenarioUsage, Usage};
use App\Domain\Common\ValueObject\ConsommationCollection;
use App\Domain\Simulation\Simulation;

final class MoteurConsommationAuxiliaire
{
    public function __construct(
        private MoteurDimensionnement $moteur_dimensionnement,
    ) {}

    public function calcule_consommation_auxiliaire_generation(Systeme $entity): ConsommationCollection
    {
        return ConsommationCollection::create(
            usage: Usage::AUXILIAIRE,
            energie: Energie::ELECTRICITE,
            callback: fn(ScenarioUsage $scenario): float => $this->caux_generation(
                bch: $entity->chauffage()->besoins()->besoins(scenario: $scenario),
                pn: $entity->generateur()->performance()->pn,
                paux: $entity->generateur()->performance()->paux,
                rdim: $entity->rdim() * $entity->installation()->rdim(),
            )
        );
    }

    public function calcule_consommation_auxiliaire_distribution(Installation $entity, Simulation $simulation): ConsommationCollection
    {
        $gv = $simulation->enveloppe()->performance()->gv;
        $situation = $simulation->audit()->situation();
        $emetteurs = $entity->emetteurs();

        $fcot = $emetteurs->reduce(
            fn(float $carry, Emetteur $item) => $carry = max($carry, $this->fcot($item->type()))
        );

        $chute_nominale_temp = $emetteurs->reduce(
            fn(float $carry, Emetteur $item) => $carry = max($carry, $this->chute_nominale_temperature($item->temperature_distribution()))
        );

        $lem = $entity->systemes()->reduce(fn(float $carry, Systeme $item) => $carry = max($carry, $this->longueur_reseau_distribution(
            fcot: $fcot,
            surface: $entity->surface(),
            niveaux_desservis: $item->reseau()->niveaux_desservis,
        )));

        $perte_emetteurs = $emetteurs->reduce(
            fn(float $carry, Emetteur $item) => $carry += $this->perte_charge(type_emetteur: $item->type(), lem: $lem,)
        );

        $pnc = $this->pnc(gv: $gv, tbase: $situation->tbase());
        $debit_circulateur = $this->debit_circultateur(pnc: $pnc, chute_nominale_temperature: $chute_nominale_temp, rdim: $entity->rdim(),);
        $pcirculateur = $this->pcirculateur(surface: $entity->surface(), perte_charge_reseau: $perte_emetteurs, debit_circulateur: $debit_circulateur,);

        return ConsommationCollection::create(
            usage: Usage::AUXILIAIRE,
            energie: Energie::ELECTRICITE,
            callback: fn(ScenarioUsage $scenario): float => $this->caux_distribution(
                pcirculateur: $pcirculateur,
                nref: Mois::reduce(fn(Mois $mois) => $situation->nref(scenario: $scenario, mois: $mois)),
            )
        );
    }

    /**
     * Consommation des auxiliaires de génération en kWh
     * 
     * @param float $bch - Besoin de chauffage en kWh
     * @param float $pn - Puissance nominale du générateur en kW
     * @param float $paux - Puissance de l'auxiliaire de génération en W
     * @param float $rdim - Ratio de dimensionnement (installation x système)
     */
    public function caux_generation(float $bch, float $pn, float $paux, float $rdim): float
    {
        return ($paux / 1000 * $bch * $rdim) / $pn;
    }

    /**
     * Consommation des auxiliaires de génération en kWh
     * 
     * @param float $pcirculateur - Puissance du circulateur en W
     * @param float $nref - Nombre d'heures de chauffage sur le mois (h)
     */
    public function caux_distribution(float $pcirculateur, float $nref): float
    {
        return $pcirculateur * $nref / 1000;
    }

    /**
     * Puissance nominale en chaud en kW
     * 
     * @param float $gv - Déperditions annuelles de l'enveloppe en W/K
     * @param float $tbase - Température de base en °C
     */
    public function pnc(float $gv, float $tbase): float
    {
        return pow(10, -3) * $gv * (20 - $tbase);
    }

    /**
     * Puissance du circulateur en W
     * 
     * @param float $surface - Surface chauffée par l'installation en m²
     * @param float $perte_charge_reseau - Pertes de charge du réseau de distribution en kPa
     * @param float $debit_circulateur - Débit nominal du circulateur en m³/h
     */
    public function pcirculateur(
        float $surface,
        float $perte_charge_reseau,
        float $debit_circulateur,
    ): float {
        $pcirculateur = 6.44;
        $pcirculateur *= pow($perte_charge_reseau * ($debit_circulateur / max(1, $surface / 400)), 0.676);
        $pcirculateur += max(1, $surface / 400);
        return \max(30, $pcirculateur);
    }

    /**
     * Débit nominal du circulateur en m³/h
     * 
     * @param float $pnc - Puissance nominale en chaud en kW
     * @param float $chute_nominale_temperature - Chute nominale de température du réseau de distribution en °C
     * @param float $rdim - Ratio de dimensionnement (installation x système)
     */
    public function debit_circultateur(float $pnc, float $chute_nominale_temperature, float $rdim): float
    {
        return ($pnc * $rdim) / (1.163 * $chute_nominale_temperature);
    }

    /**
     * Longueur du réseau de distribution en m
     * 
     * @param float $surface - Surface chauffée par l'installation en m²
     * @param int $niveaux_desservis - Nombre de niveaux désservis par l'installation
     */
    public function longueur_reseau_distribution(float $fcot, float $surface, int $niveaux_desservis,): float
    {
        return 5 * $fcot * ($niveaux_desservis + \pow($surface / $niveaux_desservis, 0.5));
    }

    /**
     * Chute de température du réseau de distribution en °C
     */
    public function chute_nominale_temperature(TemperatureDistribution $temperature_distribution): float
    {
        return $temperature_distribution->chute_nominale_temperature();
    }

    /**
     * Perte de charge de l'émetteur en kPa
     * 
     * @param float $lem - Longueur du réseau de distribution en m
     */
    public function perte_charge(TypeEmetteur $type_emetteur, float $lem): float
    {
        return 0.15 * $lem + $type_emetteur->perte_charge();
    }

    public function fcot(TypeEmetteur $type_emetteur): float
    {
        return $type_emetteur->fcot();
    }
}
