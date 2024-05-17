<?php

namespace App\Domain\Ventilation;

use App\Domain\Batiment\Enum\TypeBatiment;
use App\Domain\Common\Enum\Mois;
use App\Domain\Common\Error\EngineTableError;
use App\Domain\Ventilation\Enum\{TypeInstallation, TypeVentilation};
use App\Domain\Ventilation\Table\{Debit, DebitRepository, Pvent, PventRepository};
use App\Domain\Simulation\SimulationEngine;

final class InstallationVentilationEngine
{
    private SimulationEngine $context;
    private InstallationVentilation $input;

    /**
     * Durée de fonctionnement des auxiliaires de ventilation en heures
     */
    public final const DUREE_FONCTIONNEMENT = 8760;

    private ?Debit $table_debit;
    private ?Pvent $table_pvent;

    public function __construct(
        private DebitRepository $table_debit_repository,
        private PventRepository $table_pvent_repository,
    ) {
    }

    /**
     * Consommation annuelle des auxiliaires de ventilation en kWhef/an
     */
    public function caux(): float
    {
        return self::DUREE_FONCTIONNEMENT * ($this->pvent_moy() / 1000) * $this->ratio_temps_utilisation();
    }

    /**
     * Consommation mensuelle des auxiliaires de ventilation en kWhef/an
     */
    public function caux_j(Mois $mois): float
    {
        return $this->caux() * ($mois->jours_occupation() / Mois::NOMBRE_JOURS_OCCUPATION);
    }

    /**
     * Débit volumique conventionnel à reprendre en m3/(h.m²)
     */
    public function qvarep_conv(): float|false
    {
        return $this->table_debit()->qvarep_conv;
    }

    /**
     * Débit volumique conventionnel à souffler en m3/(h.m²)
     */
    public function qvasouf_conv(): float
    {
        return $this->table_debit()->qvasouf_conv;
    }

    /**
     * Somme des modules d'entrée d'air sous 20 Pa par unité de surface habitable en m3/(h.m2)
     */
    public function smea_conv(): float
    {
        return $this->table_debit()->smea_conv;
    }

    /**
     * Puissance moyenne des auxiliaires en W
     */
    public function pvent_moy(): float
    {
        if ($this->table_pvent()->pvent_moy) {
            return $this->table_pvent()->pvent_moy;
        }
        $qvarep_conv = $this->table_pvent()->qvarep_conv ?? $this->qvarep_conv();
        return $this->table_pvent()->pvent * $qvarep_conv * $this->surface_reference();
    }

    /**
     * Puissance des auxiliaires en W/(m³/h)
     */
    public function pvent(): null|float
    {
        return $this->table_pvent()->pvent;
    }

    /**
     * Ratio de temps d'utilisation des auxiliaires de ventilation
     */
    public function ratio_temps_utilisation(): float
    {
        return $this->type_ventilation()->ventilation_hybride()
            ? $this->type_installation()->ratio_temps_utilisation()
            : 1;
    }

    /**
     * Retoure la valeur de la table ventilation . débit
     */
    public function table_debit(): Debit
    {
        if (null === $this->table_debit) {
            throw new EngineTableError('ventilation . debit');
        }
        return $this->table_debit;
    }

    /**
     * Retoure la valeur de la table ventilation . pvent
     */
    public function table_pvent(): Pvent
    {
        if (null === $this->table_debit) {
            throw new EngineTableError('ventilation . pvent');
        }
        return $this->table_pvent;
    }

    public function fetch(): void
    {
        $this->table_debit = $this->table_debit_repository->find_by(
            type_ventilation: $this->type_ventilation(),
            type_installation: $this->type_installation(),
            annee_installation: $this->annee_installation_defaut(),
        );

        $this->table_pvent = $this->table_pvent_repository->find_by(
            type_batiment: $this->type_batiment(),
            type_ventilation: $this->type_ventilation(),
            type_installation: $this->type_installation(),
            annee_installation: $this->annee_installation_defaut(),
        );
    }

    // * Données d'entrée

    public function surface_reference(): float
    {
        return $this->context->input()->logement_collection()->surface_ventilee(id: $this->input->id());
    }

    public function type_batiment(): TypeBatiment
    {
        return $this->context->input()->batiment()->type_batiment();
    }

    public function type_installation(): ?TypeInstallation
    {
        return $this->input->type_installation();
    }

    public function type_ventilation(): TypeVentilation
    {
        return $this->input->type_ventilation();
    }

    public function annee_installation(): ?int
    {
        return $this->input->annee_installation()?->valeur();
    }

    public function annee_installation_defaut(): int
    {
        return $this->annee_installation() ?? $this->context->input()->batiment()->annee_construction()->valeur();
    }

    public function input(): InstallationVentilation
    {
        return $this->input;
    }

    public function context(): SimulationEngine
    {
        return $this->context;
    }

    public function __invoke(InstallationVentilation $input, SimulationEngine $context): self
    {
        $engine = clone $this;
        $engine->input = $input;
        $engine->context = $context;
        $engine->fetch();
        return $engine;
    }
}
