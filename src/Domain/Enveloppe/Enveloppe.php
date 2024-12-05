<?php

namespace App\Domain\Enveloppe;

use App\Domain\Audit\Audit;
use App\Domain\Enveloppe\Entity\{Parois, PlancherIntermediaire, PlancherIntermediaireCollection, Refend, RefendCollection};
use App\Domain\Enveloppe\Enum\Exposition;
use App\Domain\Enveloppe\Service\{MoteurApport, MoteurInertie, MoteurPerformance, MoteurSurfaceDeperditive};
use App\Domain\Enveloppe\ValueObject\{ApportCollection, Inertie, Performance, Permeabilite};
use App\Domain\Lnc\{Lnc, LncCollection};
use App\Domain\PontThermique\{PontThermique, PontThermiqueCollection};
use App\Domain\Simulation\Simulation;
use Webmozart\Assert\Assert;

/**
 * @see App\Domain\Audit\Audit::enveloppe()
 */
final class Enveloppe
{
    private ?Inertie $inertie = null;
    private ?Permeabilite $permeabilite = null;
    private ?Performance $performance = null;
    private ?ApportCollection $apports = null;

    public function __construct(
        private readonly Audit $audit,
        private Exposition $exposition,
        private ?float $q4pa_conv,
        private LncCollection $locaux_non_chauffes,
        private Parois $parois,
        private PontThermiqueCollection $ponts_thermiques,
        private RefendCollection $refends,
        private PlancherIntermediaireCollection $planchers_intermediaires,
    ) {}

    public function controle(): void
    {
        Assert::greaterThan($this->q4pa_conv, 0);
        $this->locaux_non_chauffes->controle();
        $this->ponts_thermiques->controle();
        $this->refends->controle();
        $this->planchers_intermediaires->controle();
        $this->parois->controle();
    }

    public function reinitialise(): void
    {
        $this->inertie = null;
        $this->permeabilite = null;
        $this->performance = null;
        $this->apports = null;
    }

    public function calcule_surface_deperditive(MoteurSurfaceDeperditive $moteur): self
    {
        $this->parois->murs()->calcule_surface_deperditive($moteur->moteur_surface_deperditive_mur());
        $this->parois->planchers_bas()->calcule_surface_deperditive($moteur->moteur_surface_deperditive_plancher_bas());
        $this->parois->planchers_hauts()->calcule_surface_deperditive($moteur->moteur_surface_deperditive_plancher_haut());
        $this->locaux_non_chauffes->calcule_surface_deperditive($moteur->moteur_surface_deperditive_lnc());
        return $this;
    }

    public function calcule_inertie(MoteurInertie $moteur): self
    {
        $this->inertie = $moteur->calcule_inertie($this);
        return $this;
    }

    public function calcule_performance(MoteurPerformance $moteur, Simulation $simulation): self
    {
        $this->locaux_non_chauffes->calcule_performance($moteur->moteur_performance_lnc());
        $this->parois->murs()->calcule_performance($moteur->moteur_performance_mur(), $simulation);
        $this->parois->planchers_bas()->calcule_performance($moteur->moteur_performance_plancher_bas(), $simulation);
        $this->parois->planchers_hauts()->calcule_performance($moteur->moteur_performance_plancher_haut(), $simulation);
        $this->parois->baies()->calcule_performance($moteur->moteur_performance_baie());
        $this->parois->portes()->calcule_performance($moteur->moteur_performance_porte());
        $this->ponts_thermiques->calcule_performance($moteur->moteur_performance_pont_thermique());
        $this->permeabilite = $moteur->calcule_permeabilite($this, $simulation);
        $this->performance = $moteur->calcule_performance($this);
        return $this;
    }

    public function calcule_apport(MoteurApport $moteur, Simulation $simulation): self
    {
        $this->locaux_non_chauffes->calcule_ensoleillement($moteur->moteur_ensoleillement_lnc());
        $this->parois->baies()->calcule_ensoleillement($moteur->moteur_ensoleillement_baie());
        $this->apports = $moteur->calcule_apport($this, $simulation);
        return $this;
    }

    public function audit(): Audit
    {
        return $this->audit;
    }

    public function exposition(): Exposition
    {
        return $this->exposition;
    }

    public function q4pa_conv(): ?float
    {
        return $this->q4pa_conv;
    }

    public function parois(): Parois
    {
        return $this->parois;
    }

    public function locaux_non_chauffes(): LncCollection
    {
        return $this->locaux_non_chauffes;
    }

    public function add_local_non_chauffe(Lnc $entity): self
    {
        $this->locaux_non_chauffes->add($entity);
        return $this;
    }

    public function ponts_thermiques(): PontThermiqueCollection
    {
        return $this->ponts_thermiques;
    }

    public function add_pont_thermique(PontThermique $entity): self
    {
        $this->ponts_thermiques->add($entity);
        return $this;
    }

    public function refends(): RefendCollection
    {
        return $this->refends;
    }

    public function add_refend(Refend $entity): self
    {
        $this->refends->add($entity);
        return $this;
    }

    public function planchers_intermediaires(): PlancherIntermediaireCollection
    {
        return $this->planchers_intermediaires;
    }

    public function add_plancher_intermediaire(PlancherIntermediaire $entity): self
    {
        $this->planchers_intermediaires->add($entity);
        return $this;
    }

    public function inertie(): ?Inertie
    {
        return $this->inertie;
    }

    public function permeabilite(): ?Permeabilite
    {
        return $this->permeabilite;
    }

    public function performance(): ?Performance
    {
        return $this->performance;
    }

    public function apports(): ?ApportCollection
    {
        return $this->apports;
    }

    // * hepers

    public function annee_construction_batiment(): int
    {
        return $this->audit->annee_construction_batiment();
    }
}
