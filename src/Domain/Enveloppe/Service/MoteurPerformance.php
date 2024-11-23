<?php

namespace App\Domain\Enveloppe\Service;

use App\Domain\Common\Enum\Enum;
use App\Domain\Enveloppe\Data\Q4paConvRepository;
use App\Domain\Enveloppe\Enveloppe;
use App\Domain\Enveloppe\Enum\Exposition;
use App\Domain\Enveloppe\ValueObject\{Permeabilite, Performance};
use App\Domain\Lnc\Service\MoteurPerformance as MoteurPerformanceLnc;
use App\Domain\Baie\Service\MoteurPerformance as MoteurPerformanceBaie;
use App\Domain\Mur\Service\MoteurPerformance as MoteurPerformanceMur;
use App\Domain\PlancherBas\Service\MoteurPerformance as MoteurPerformancePlancherBas;
use App\Domain\PlancherHaut\Service\MoteurPerformance as MoteurPerformancePlancherHaut;
use App\Domain\Porte\Service\MoteurPerformance as MoteurPerformancePorte;
use App\Domain\PontThermique\Service\MoteurPerformance as MoteurPerformancePontThermique;
use App\Domain\Simulation\Simulation;

/**
 * @uses \App\Domain\Lnc\Service\MoteurPerformance
 * @uses \App\Domain\Mur\Service\MoteurPerformance
 * @uses \App\Domain\PlancherBas\Service\MoteurPerformance
 * @uses \App\Domain\PlancherHaut\Service\MoteurPerformance
 * @uses \App\Domain\Baie\Service\MoteurPerformance
 * @uses \App\Domain\Porte\Service\MoteurPerformance
 * @uses \App\Domain\PontThermique\Service\MoteurPerformance
 * @uses \App\Domain\Ventilation\Service\MoteurPerformance
 */
final class MoteurPerformance
{
    public function __construct(
        private MoteurPerformanceLnc $moteur_performance_lnc,
        private MoteurPerformanceMur $moteur_performance_mur,
        private MoteurPerformancePlancherBas $moteur_performance_plancher_bas,
        private MoteurPerformancePlancherHaut $moteur_performance_plancher_haut,
        private MoteurPerformanceBaie $moteur_performance_baie,
        private MoteurPerformancePorte $moteur_performance_porte,
        private MoteurPerformancePontThermique $moteur_performance_pont_thermique,
        private Q4paConvRepository $q4pa_conv_repository,
    ) {}

    public function calcule_performance(Enveloppe $entity): ?Performance
    {
        if (null === $permeabilite = $entity->permeabilite())
            return null;

        return Performance::create(
            sdep: $entity->parois()->surface_deperditive(),
            dp: ($dp = $entity->parois()->deperdition()),
            pt: ($pt = $entity->ponts_thermiques()->pt()),
            dr: ($dr = $this->dr(hvent: $permeabilite->hvent, hperm: $permeabilite->hperm)),
            gv: $this->gv(dp: $dp, pt: $pt, dr: $dr),
        );
    }

    public function calcule_permeabilite(Enveloppe $entity, Simulation $simulation): Permeabilite
    {
        $qvarep_conv = $simulation->ventilation()->installations()->qvarep_conv();
        $qvasouf_conv = $simulation->ventilation()->installations()->qvasouf_conv();
        $smea_conv = $simulation->ventilation()->installations()->smea_conv();
        $presence_joint_menuiseries = $this->calcule_presence_joint_menuiserie($entity);
        $isolation_murs_plafonds = $this->calcule_isolation_murs_plafonds($entity);

        $q4pa_conv = $this->q4pa_conv(
            type_batiment: $entity->audit()->type_batiment(),
            annee_construction: $entity->audit()->annee_construction_batiment(),
            presence_joints_menuiseries: $presence_joint_menuiseries,
            isolation_murs_plafonds: $isolation_murs_plafonds,
        );
        $q4pa_env = $this->q4pa_env(
            q4pa_conv: $q4pa_conv,
            surface_deperditive: $entity->parois()->surface_deperditive() - $entity->parois()->planchers_bas()->surface_deperditive(),
        );
        $q4pa = $this->q4pa(
            q4pa_env: $q4pa_env,
            smea_conv: $smea_conv,
            surface_habitable: $entity->audit()->surface_habitable_reference(),
        );
        $n50 = $this->n50(
            q4pa: $q4pa,
            surface_habitable: $entity->audit()->surface_habitable_reference(),
            hauteur_sous_plafond: $entity->audit()->hauteur_sous_plafond_reference(),
        );
        $qvinf = $this->qvinf(
            n50: $n50,
            e: $this->e($entity->exposition()),
            f: $this->f($entity->exposition()),
            qvasouf_conv: $qvasouf_conv,
            qvarep_conv: $qvasouf_conv,
            hauteur_sous_plafond: $entity->audit()->hauteur_sous_plafond_reference(),
            surface_habitable: $entity->audit()->surface_habitable_reference(),
        );
        $hvent = $this->hvent(
            qvarep_conv: $qvarep_conv,
            surface_habitable: $entity->audit()->surface_habitable_reference(),
        );
        $hperm = $this->hperm(qvinf: $qvinf);

        return Permeabilite::create(
            q4pa_conv: $q4pa_conv,
            hvent: $hvent,
            hperm: $hperm,
        );
    }

    public function calcule_presence_joint_menuiserie(Enveloppe $entity): bool
    {
        $surface_totale = $entity->parois()->baies()->surface() + $entity->parois()->portes()->surface();
        $surface_avec_joint = $entity->parois()->baies()->filter_by_presence_joint(true)->surface();
        $surface_avec_joint += $entity->parois()->portes()->filter_by_presence_joint(true)->surface();
        return $surface_avec_joint > $surface_totale / 2;
    }

    public function calcule_isolation_murs_plafonds(Enveloppe $entity): bool
    {
        $surface_totale = $entity->parois()->murs()->surface_deperditive() + $entity->parois()->planchers_hauts()->surface_deperditive();
        $surface_isolee = $entity->parois()->murs()->filter_by_isolation(true)->surface_deperditive();
        $surface_isolee += $entity->parois()->planchers_hauts()->filter_by_isolation(true)->surface_deperditive();
        return $surface_isolee > $surface_totale / 2;
    }

    public function moteur_performance_lnc(): MoteurPerformanceLnc
    {
        return $this->moteur_performance_lnc;
    }

    public function moteur_performance_mur(): MoteurPerformanceMur
    {
        return $this->moteur_performance_mur;
    }

    public function moteur_performance_plancher_bas(): MoteurPerformancePlancherBas
    {
        return $this->moteur_performance_plancher_bas;
    }

    public function moteur_performance_plancher_haut(): MoteurPerformancePlancherHaut
    {
        return $this->moteur_performance_plancher_haut;
    }

    public function moteur_performance_baie(): MoteurPerformanceBaie
    {
        return $this->moteur_performance_baie;
    }

    public function moteur_performance_porte(): MoteurPerformancePorte
    {
        return $this->moteur_performance_porte;
    }

    public function moteur_performance_pont_thermique(): MoteurPerformancePontThermique
    {
        return $this->moteur_performance_pont_thermique;
    }

    /**
     * Déperditions thermiques de l'enveloppe en W/K
     */
    public function gv(float $dp, float $pt, float $dr): float
    {
        return $dp + $pt + $dr;
    }

    /**
     * Déperditions thermiques par renouvellement d'air en W/K
     */
    public function dr(float $hvent, float $hperm): float
    {
        return $hvent + $hperm;
    }

    public function hvent(float $qvarep_conv, float $surface_habitable): float
    {
        return 0.34 * $qvarep_conv * $surface_habitable;
    }

    public function hperm(float $qvinf): float
    {
        return 0.34 * $qvinf;
    }

    public function qvinf(
        float $n50,
        float $e,
        float $f,
        float $qvasouf_conv,
        float $qvarep_conv,
        float $hauteur_sous_plafond,
        float $surface_habitable,
    ): float {
        $qvinf = $hauteur_sous_plafond * $surface_habitable * $n50 * $e;
        $qvinf /= 1 + $e / $f * \pow(($qvasouf_conv - $qvarep_conv) / ($hauteur_sous_plafond * $n50), 2);

        return $qvinf;
    }

    /**
     * Renouvellement d'air sous 50 Pascals en h-1
     */
    public function n50(float $q4pa, float $surface_habitable, float $hauteur_sous_plafond): float
    {
        return $q4pa / (\pow(4 / 50, 2 / 3) * $surface_habitable * $hauteur_sous_plafond);
    }

    /**
     * Perméabilité sous 4 Pa de la zone en m3/h
     */
    public function q4pa(float $q4pa_env, float $smea_conv, float $surface_habitable): float
    {
        return $q4pa_env + 0.45 * $smea_conv * $surface_habitable;
    }

    /**
     * Perméabilité de l'enveloppe en m3/h
     * 
     * @param float $q4pa_conv - valeur conventionnelle de la perméabilité sous 4Pa en m3/(h.m2)
     * @param float $surface_deperditive - surface des parois déperditives hors plancher bas (m²)
     */
    public function q4pa_env(float $q4pa_conv, float $surface_deperditive): float
    {
        return $q4pa_conv * $surface_deperditive;
    }

    /**
     * Coefficients de protection
     */
    public function e(Exposition $exposition): float
    {
        return $exposition->e();
    }

    /**
     * Coefficients de protection
     */
    public function f(Exposition $exposition): float
    {
        return $exposition->f();
    }

    /**
     * Valeur conventionnelle de la perméabilité sous 4Pa en m3/(h.m2)
     */
    public function q4pa_conv(
        Enum $type_batiment,
        int $annee_construction,
        bool $presence_joints_menuiseries,
        bool $isolation_murs_plafonds,
    ): float {
        if (null === $valeur = $this->q4pa_conv_repository->find_by(
            type_batiment: $type_batiment,
            annee_construction: $annee_construction,
            presence_joints_menuiserie: $presence_joints_menuiseries,
            isolation_murs_plafonds: $isolation_murs_plafonds,
        ));

        return $valeur->q4pa_conv;
    }
}
