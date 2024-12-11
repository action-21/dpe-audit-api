<?php

namespace App\Domain\Chauffage\Service;

use App\Domain\Chauffage\Entity\{Generateur, Installation, Systeme};
use App\Domain\Chauffage\Enum\{Configuration, TypeChauffage};
use App\Domain\Common\Enum\{Mois, ScenarioUsage};
use App\Domain\Simulation\Simulation;

final class MoteurDimensionnement
{
    public function calcule_dimensionnement(Systeme $entity): float
    {
        $rdim = $this->calcule_dimensionnement_installation($entity->installation());
        return $rdim *= $this->calcule_dimensionnement_systeme($entity);
    }

    public function calcule_dimensionnement_installation(Installation $entity): float
    {
        return $this->rdim_installation(
            surface_installation: $entity->surface(),
            surface_installations: $entity->chauffage()->installations()->surface(),
        );
    }

    public function calcule_dimensionnement_systeme(Systeme $entity): float
    {
        $configuration = Configuration::determine($entity->installation());
        $somme_pn_base = $entity->installation()->systemes()->filter_by_type_chauffage(TypeChauffage::CHAUFFAGE_CENTRAL)->has_pn()
            ? $entity->installation()->systemes()->filter_by_type_chauffage(TypeChauffage::CHAUFFAGE_CENTRAL)->pn()
            : null;

        return match (true) {
            $configuration->is_base($entity) => $this->rdim_base(
                configuration: $configuration,
                appoint: $entity->installation()->systemes()->has_systeme_divise(),
                systemes_base: $entity->installation()->systemes()->has_systeme_central()
                    ? $entity->installation()->systemes()->filter_by_type_chauffage(TypeChauffage::CHAUFFAGE_CENTRAL)->count()
                    : $entity->installation()->systemes()->count(),
                pn_base: $entity->generateur()->signaletique()?->pn,
                somme_pn_base: $somme_pn_base,
            ),
            $configuration->is_releve($entity) => $this->rdim_releve(
                configuration: $configuration,
                appoint: $entity->installation()->systemes()->has_systeme_divise(),
            ),
            $configuration->is_appoint($entity) => $this->rdim_appoint(
                systemes: $entity->installation()->systemes()->filter_by_type_chauffage(TypeChauffage::CHAUFFAGE_CENTRAL)->count(),
            ),
        };
    }

    public function calcule_taux_bch(Systeme $entity, Simulation $simulation, ScenarioUsage $scenario): float
    {
        $configuration = Configuration::determine($entity->installation());
        $configuration_collective = $configuration->configuration_collective($entity->installation());

        if (false === $configuration_collective || false === $entity->installation()->systemes()->has_pn())
            return 1;

        $besoins = $entity->chauffage()->besoins();
        $situation = $simulation->audit()->situation();
        $dh14 = Mois::reduce(fn(float $carry, Mois $mois): float => $carry += $situation->dh14($mois));
        $pe = $this->calcule_pe($entity, $scenario);
        $t = $this->t(bch: $besoins->besoins(scenario: $scenario), dh14: $dh14, pe: $pe);

        $bch_base = Mois::reduce(fn(float $carry, Mois $mois): float => $carry += $this->bch_base_releve(
            bch: $besoins->besoins(scenario: $scenario, mois: $mois),
            dht: $this->dht(
                nref: $situation->nref(scenario: $scenario, mois: $mois),
                text: $situation->text(mois: $mois),
                tbase: $situation->tbase(),
                t: $t,
            ),
            dh14: $dh14,
        ));

        $taux_bch = \min($bch_base / $besoins->besoins(scenario: $scenario), 1);
        return $configuration->is_appoint($entity) ? 1 - $taux_bch : $taux_bch;
    }

    private function calcule_pe(Systeme $entity, ScenarioUsage $scenario): float
    {
        $pn = $entity->generateur()->signaletique()->pn;
        $rd = $re = $rr = 1;

        /** @var Systeme */
        foreach ($entity->chauffage()->installations()->search_systemes_by_generateur($entity->generateur()->id()) as $systeme) {
            $rd = \min($rd, $systeme->rendements()->rd(scenario: $scenario));
            $re = \min($re, $systeme->rendements()->re(scenario: $scenario));
            $rr = \min($rr, $systeme->rendements()->rr(scenario: $scenario));
        }
        return $this->pe(pn: $pn, rd: $rd, re: $re, rr: $rr);
    }

    public function calcule_pch(Generateur $entity, Simulation $simulation): float
    {
        $tbase = $simulation->audit()->situation()->tbase();
        $gv = $simulation->enveloppe()?->performance()->gv;
        $rdim = 0;

        /** @var Installation */
        foreach ($entity->chauffage()->installations() as $installation) {
            /** @var Systeme */
            foreach ($installation->systemes() as $systeme) {
                if (false === $entity->id()->compare($systeme->generateur()->id()))
                    continue;

                $rdim += $this->calcule_dimensionnement($systeme, $simulation);
            }
        }
        return $this->pch(
            gv: $gv,
            tbase: $tbase,
            rdim: $rdim,
            ratio_proratisation: $simulation->audit()->ratio_proratisation(),
            installation_collective: $entity->generateur_collectif(),
        );
    }


    /**
     * Ratio de dimensionnement de l'installation
     * 
     * @param float $surface_installation - Surface de l'installation
     * @param float $surface_installations - Surface totale des installations
     */
    public function rdim_installation(float $surface_installation, float $surface_installations): float
    {
        return $surface_installations > 0 ? $surface_installation / $surface_installations : 0;
    }

    /**
     * Ratio de dimensionnement du système en base dans une configuration base + relève
     * 
     * @param bool $appoint - Présence d'un ou plusieurs systèmes d'appoint
     * @param int $systemes_base - Nombre de systèmes de base de l'installation
     * @param float $pn_base - Puissance nominale du système de base
     * @param float $somme_pn_base - Somme des puissances nominales des systèmes de base
     */
    public function rdim_base(
        Configuration $configuration,
        bool $appoint,
        int $systemes_base,
        ?float $pn_base,
        ?float $somme_pn_base,
    ): float {
        $rdim_appoint = $appoint ? 0.25 : 0;

        return match ($configuration) {
            Configuration::BASE => 1,
            Configuration::BASE_APPOINT => 1 * (1 - $rdim_appoint),
            Configuration::BASE_BOIS_RELEVE_PAC,
            Configuration::BASE_BOIS_RELEVE_CHAUDIERE => 0.75 * (1 - $rdim_appoint),
            Configuration::BASE_PAC_RELEVE_CHAUDIERE => 0.8 * (1 - $rdim_appoint),
            Configuration::AUTRES => ($pn_base && $somme_pn_base) ? $pn_base / $somme_pn_base : 1 / $systemes_base,
        };
    }

    /**
     * Beosins de chauffage couverts par le système central collectif (base ou relève) pour le mois
     * 
     * @param float $bch - Besoin de chauffage sur le mois en kWh
     * @param float $dht - Degré heure base T sur l'année
     * @param float $dh14 - Degrés heures de base 14 en période de chauffe sur l'année en °C.h
     */
    public function bch_base_releve(float $bch, float $dht, float $dh14): float
    {
        return $bch * \min(1 - $dht / $dh14, 1);
    }

    /**
     * Ratio de dimensionnement du système en relève dans une configuration base + relève
     * 
     * @param int $systemes - Nombre de systèmes d'appoint de l'installation
     */
    public function rdim_releve(Configuration $configuration, bool $appoint): float
    {
        $rdim_appoint = $appoint ? 0.25 : 0;

        return match ($configuration) {
            Configuration::BASE_BOIS_RELEVE_PAC,
            Configuration::BASE_BOIS_RELEVE_CHAUDIERE => 0.25 * (1 - $rdim_appoint),
            Configuration::BASE_PAC_RELEVE_CHAUDIERE => 0.2 * (1 - $rdim_appoint),
            default => 0,
        };
    }

    /**
     * Ratio de dimensionnement du système d'appoint
     * 
     * @param int $systemes - Nombre de systèmes d'appoint de l'installation
     */
    public function rdim_appoint(int $systemes,): float
    {
        return 0.25 * (1 / $systemes);
    }

    /**
     * Ratio de dimensionnement d'une émetteur
     * 
     * @param int $emissions - Nombre d'émissions associé au système
     */
    public function rdim_emission(int $emissions): float
    {
        return $emissions ? 1 / $emissions : 0;
    }

    /**
     * Température de dimensionnement en °C
     * 
     * @param float $bch - Besoin de chauffage en kW
     * @param float $dh14 - Degrés heures de base 14 en période de chauffe sur l'année en °C.h
     * @param float $pe - Puissance utile du générateur collectif en base en kW
     */
    public function t(float $bch, float $dh14, float $pe): float
    {
        return 14 - ($pe * $dh14 / $bch);
    }

    /**
     * Puissance utile du générateur collectif en base en kW
     * 
     * @param float $pn - Puissance nominale du générateur en kW
     * @param float $rd - Ratio de distribution de l'installation alimentée par le générateur
     * @param float $re - Ratio d'émission de l'installation alimentée par le générateur
     * @param float $rr - Ratio de régulation de l'installation alimentée par le générateur
     */
    public function pe(float $pn, float $rd, float $re, float $rr): float
    {
        return $pn * $rd * $re * $rr;
    }

    /**
     * Degré heure base T sur le mois
     * 
     * @param float $nref - Nombre d'heures de chauffage pour le mois en h
     * @param float $text - Température extérieure moyenne en période de chauffe sur le mois en °C
     * @param float $tbase - Température extérieure de base en °C
     * @param float $t - Température de dimensionnement en °C
     */
    public function dht(float $nref, float $text, float $tbase, float $t): float
    {
        $x = 0.5 * (($t - $tbase) / ($text - $tbase));
        return $nref * ($text - $tbase) * \pow($x, 5) * (14 - 28 * $x + 20 * \pow($x, 2) - 5 * \pow($x, 3));
    }

    /**
     * Puissance de dimensionnement du besoin de chauffage en kW
     * 
     * @param float $gv - Déperditions thermiques de l'enveloppe en W/K
     * @param float $tbase - Température extérieure de base en °C
     * @param float $rdim - Ratio de dimensionnement des installations alimentées par le générateur
     * @param float $ratio_proratisation - Ratio de proratisation
     */
    public function pch(
        float $gv,
        float $tbase,
        float $rdim,
        float $ratio_proratisation,
        bool $installation_collective,
    ): float {
        $pch = (1.2 * $gv * (19 - $tbase)) / (1000 * \pow(0.95, 3)) * $rdim;
        return $installation_collective ? $pch * (1 / $ratio_proratisation) : $pch;
    }
}
