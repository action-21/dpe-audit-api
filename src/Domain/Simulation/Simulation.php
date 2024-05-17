<?php

namespace App\Domain\Simulation;

use App\Domain\Audit\Audit;
use App\Domain\Baie\BaieCollection;
use App\Domain\Batiment\Batiment;
use App\Domain\Chauffage\InstallationChauffageCollection;
use App\Domain\Climatisation\InstallationClimatisationCollection;
use App\Domain\Ecs\InstallationEcsCollection;
use App\Domain\Enveloppe\Enveloppe;
use App\Domain\Lnc\LncCollection;
use App\Domain\Logement\LogementCollection;
use App\Domain\MasqueLointain\MasqueLointainCollection;
use App\Domain\MasqueProche\MasqueProcheCollection;
use App\Domain\Mur\MurCollection;
use App\Domain\PlancherBas\PlancherBasCollection;
use App\Domain\PlancherHaut\PlancherHautCollection;
use App\Domain\PlancherIntermediaire\PlancherIntermediaireCollection;
use App\Domain\PontThermique\PontThermiqueCollection;
use App\Domain\Porte\PorteCollection;
use App\Domain\Refend\RefendCollection;
use App\Domain\Ventilation\InstallationVentilationCollection;

final class Simulation
{
    public function __construct(
        public readonly Audit $audit,
        public readonly Batiment $batiment,
        public readonly Enveloppe $enveloppe,
        public readonly LogementCollection $logement_collection,
        public readonly LncCollection $local_non_chauffe_collection,
        public readonly MasqueProcheCollection $masque_proche_collection,
        public readonly MasqueLointainCollection $masque_lointain_collection,
        public readonly BaieCollection $baie_collection,
        public readonly MurCollection $mur_collection,
        public readonly PlancherBasCollection $plancher_bas_collection,
        public readonly PlancherHautCollection $plancher_haut_collection,
        public readonly PlancherIntermediaireCollection $plancher_intermediaire_collection,
        public readonly PontThermiqueCollection $pont_thermique_collection,
        public readonly PorteCollection $porte_collection,
        public readonly RefendCollection $refend_collection,
        public readonly InstallationChauffageCollection $chauffage_collection,
        public readonly InstallationEcsCollection $ecs_collection,
        public readonly InstallationClimatisationCollection $climatisation_collection,
        public readonly InstallationVentilationCollection $ventilation_collection,
    ) {
    }

    public function audit(): Audit
    {
        return $this->audit;
    }

    public function batiment(): Batiment
    {
        return $this->batiment;
    }

    public function enveloppe(): Enveloppe
    {
        return $this->enveloppe;
    }

    public function logement_collection(): LogementCollection
    {
        return $this->logement_collection;
    }

    public function local_non_chauffe_collection(): LncCollection
    {
        return $this->local_non_chauffe_collection;
    }

    public function masque_proche_collection(): MasqueProcheCollection
    {
        return $this->masque_proche_collection;
    }

    public function masque_lointain_collection(): MasqueLointainCollection
    {
        return $this->masque_lointain_collection;
    }

    public function baie_collection(): BaieCollection
    {
        return $this->baie_collection;
    }

    public function mur_collection(): MurCollection
    {
        return $this->mur_collection;
    }

    public function plancher_bas_collection(): PlancherBasCollection
    {
        return $this->plancher_bas_collection;
    }

    public function plancher_haut_collection(): PlancherHautCollection
    {
        return $this->plancher_haut_collection;
    }

    public function plancher_intermediaire_collection(): PlancherIntermediaireCollection
    {
        return $this->plancher_intermediaire_collection;
    }

    public function pont_thermique_collection(): PontThermiqueCollection
    {
        return $this->pont_thermique_collection;
    }

    public function porte_collection(): PorteCollection
    {
        return $this->porte_collection;
    }

    public function refend_collection(): RefendCollection
    {
        return $this->refend_collection;
    }

    public function chauffage_collection(): InstallationChauffageCollection
    {
        return $this->chauffage_collection;
    }

    public function ecs_collection(): InstallationEcsCollection
    {
        return $this->ecs_collection;
    }

    public function climatisation_collection(): InstallationClimatisationCollection
    {
        return $this->climatisation_collection;
    }

    public function ventilation_collection(): InstallationVentilationCollection
    {
        return $this->ventilation_collection;
    }
}
