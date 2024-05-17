<?php

namespace App\Domain\Simulation;

use App\Domain\Audit\AuditEngine;
use App\Domain\Audit\Enum\PerimetreApplication;
use App\Domain\Baie\BaieEngineCollection;
use App\Domain\Batiment\BatimentEngine;
use App\Domain\Chauffage\InstallationChauffageEngineCollection;
use App\Domain\Climatisation\InstallationClimatisationEngineCollection;
use App\Domain\Ecs\InstallationEcsEngineCollection;
use App\Domain\Enveloppe\EnveloppeEngine;
use App\Domain\Lnc\LncEngineCollection;
use App\Domain\Logement\LogementEngineCollection;
use App\Domain\MasqueLointain\MasqueLointainEngineCollection;
use App\Domain\MasqueProche\MasqueProcheEngineCollection;
use App\Domain\Mur\MurEngineCollection;
use App\Domain\PlancherBas\PlancherBasEngineCollection;
use App\Domain\PlancherHaut\PlancherHautEngineCollection;
use App\Domain\PontThermique\PontThermiqueEngineCollection;
use App\Domain\Porte\PorteEngineCollection;
use App\Domain\Ventilation\InstallationVentilationEngineCollection;

final class SimulationEngine
{
    private Simulation $input;

    public function __construct(
        private AuditEngine $audit_engine,
        private BatimentEngine $batiment_engine,
        private EnveloppeEngine $enveloppe_engine,
        private LogementEngineCollection $logement_engine_collection,
        private LncEngineCollection $local_non_chauffe_engine_collection,
        private MasqueProcheEngineCollection $masque_proche_engine_collection,
        private MasqueLointainEngineCollection $masque_lointain_engine_collection,
        private BaieEngineCollection $baie_engine_collection,
        private MurEngineCollection $mur_engine_collection,
        private PlancherBasEngineCollection $plancher_bas_engine_collection,
        private PlancherHautEngineCollection $plancher_haut_engine_collection,
        private PontThermiqueEngineCollection $pont_thermique_engine_collection,
        private PorteEngineCollection $porte_engine_collection,
        private InstallationChauffageEngineCollection $chauffage_engine_collection,
        private InstallationEcsEngineCollection $ecs_engine_collection,
        private InstallationClimatisationEngineCollection $climatisation_engine_collection,
        private InstallationVentilationEngineCollection $ventilation_engine_collection,
    ) {
    }

    public function audit_engine(): AuditEngine
    {
        return $this->audit_engine;
    }

    public function batiment_engine(): BatimentEngine
    {
        return $this->batiment_engine;
    }

    public function enveloppe_engine(): EnveloppeEngine
    {
        return $this->enveloppe_engine;
    }

    /*
    public function photovoltaique_engine(): ?InstallationPhotovoltaiqueEngine
    {
        return $this->input->photovoltaique() ? $this->photovoltaique_engine : null;
    }
    */

    public function logement_engine_collection(): LogementEngineCollection
    {
        return $this->logement_engine_collection;
    }

    public function local_non_chauffe_engine_collection(): LncEngineCollection
    {
        return $this->local_non_chauffe_engine_collection;
    }

    public function masque_proche_engine_collection(): MasqueProcheEngineCollection
    {
        return $this->masque_proche_engine_collection;
    }

    public function masque_lointain_engine_collection(): MasqueLointainEngineCollection
    {
        return $this->masque_lointain_engine_collection;
    }

    public function baie_engine_collection(): BaieEngineCollection
    {
        return $this->baie_engine_collection;
    }

    public function mur_engine_collection(): MurEngineCollection
    {
        return $this->mur_engine_collection;
    }

    public function plancher_bas_engine_collection(): PlancherBasEngineCollection
    {
        return $this->plancher_bas_engine_collection;
    }

    public function plancher_haut_engine_collection(): PlancherHautEngineCollection
    {
        return $this->plancher_haut_engine_collection;
    }

    public function pont_thermique_engine_collection(): PontThermiqueEngineCollection
    {
        return $this->pont_thermique_engine_collection;
    }

    public function porte_engine_collection(): PorteEngineCollection
    {
        return $this->porte_engine_collection;
    }

    public function ventilation_engine_collection(): InstallationVentilationEngineCollection
    {
        return $this->ventilation_engine_collection;
    }

    public function climatisation_engine_collection(): InstallationClimatisationEngineCollection
    {
        return $this->climatisation_engine_collection;
    }

    public function chauffage_engine_collection(): InstallationChauffageEngineCollection
    {
        return $this->chauffage_engine_collection;
    }

    public function ecs_engine_collection(): InstallationEcsEngineCollection
    {
        return $this->ecs_engine_collection;
    }

    public function input(): Simulation
    {
        return $this->input;
    }

    /**
     * Nombre de logements de référence
     */
    public function nombre_logements_reference(): int
    {
        return $this->input->audit()->perimetre_application() === PerimetreApplication::IMMEUBLE
            ? $this->input->batiment()->nombre_logements()->valeur()
            : $this->input->logement_collection()->count();
    }

    /**
     * Surface habitable de référence en m²
     */
    public function surface_habitable_reference(): float
    {
        return $this->input->audit()->perimetre_application() === PerimetreApplication::IMMEUBLE
            ? $this->input->batiment()->surface_habitable()
            : $this->input->logement_collection()->surface_habitable();
    }

    /**
     * Surface habitable en m²
     */
    public function surface_habitable(): float
    {
        return $this->input->logement_collection()->surface_habitable();
    }

    /**
     * Hauteur sous plafond de référence en m
     */
    public function hauteur_sous_plafond_reference(): float
    {
        return $this->input->audit()->perimetre_application() === PerimetreApplication::IMMEUBLE
            ? $this->input->batiment()->hauteur_sous_plafond_moyenne()
            : $this->input->logement_collection()->hauteur_sous_plafond();
    }

    /**
     * Volume habitable de référence en m²
     */
    public function volume_habitable_reference(): float
    {
        return $this->surface_habitable_reference() * $this->hauteur_sous_plafond_reference();
    }

    /**
     * Volume habitable en m²
     */
    public function volume_habitable(): float
    {
        return $this->surface_habitable() * $this->hauteur_sous_plafond_reference();
    }

    /**
     * Surface climatisée de référence en m²
     */
    public function surface_fr_reference(): float
    {
        return $this->surface_habitable_reference() * $this->surface_fr() / $this->surface_habitable();
    }

    /**
     * Surface climatisée en m²
     */
    public function surface_fr(): float
    {
        return $this->input->logement_collection()->surface_climatisee();
    }

    public function __invoke(Simulation $input): self
    {
        $simulation = clone $this;
        $simulation->input = $input;
        $simulation->audit_engine = ($this->audit_engine)($input->audit());
        $simulation->batiment_engine = ($this->batiment_engine)($input->batiment(), $simulation);
        $simulation->enveloppe_engine = ($this->enveloppe_engine)($input->enveloppe(), $simulation);
        $simulation->logement_engine_collection =
            ($this->logement_engine_collection)($input->logement_collection(), $simulation);
        $simulation->local_non_chauffe_engine_collection =
            ($this->local_non_chauffe_engine_collection)($input->local_non_chauffe_collection(), $simulation);
        $simulation->masque_proche_engine_collection =
            ($this->masque_proche_engine_collection)($input->masque_proche_collection(), $simulation);
        $simulation->masque_lointain_engine_collection =
            ($this->masque_lointain_engine_collection)($input->masque_lointain_collection(), $simulation);
        $simulation->baie_engine_collection =
            ($this->baie_engine_collection)($input->baie_collection(), $simulation);
        $simulation->mur_engine_collection =
            ($this->mur_engine_collection)($input->mur_collection(), $simulation);
        $simulation->plancher_bas_engine_collection =
            ($this->plancher_bas_engine_collection)($input->plancher_bas_collection(), $simulation);
        $simulation->plancher_haut_engine_collection =
            ($this->plancher_haut_engine_collection)($input->plancher_haut_collection(), $simulation);
        $simulation->pont_thermique_engine_collection =
            ($this->pont_thermique_engine_collection)($input->pont_thermique_collection(), $simulation);
        $simulation->porte_engine_collection =
            ($this->porte_engine_collection)($input->porte_collection(), $simulation);
        $simulation->ventilation_engine_collection =
            ($this->ventilation_engine_collection)($input->ventilation_collection(), $simulation);
        $simulation->ecs_engine_collection =
            ($this->ecs_engine_collection)($input->ecs_collection(), $simulation);
        $simulation->chauffage_engine_collection =
            ($this->chauffage_engine_collection)($input->chauffage_collection(), $simulation);
        $simulation->climatisation_engine_collection =
            ($this->climatisation_engine_collection)($input->climatisation_collection(), $simulation);

        return $simulation;
    }
}
