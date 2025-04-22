<?php

namespace App\Domain\Enveloppe\Engine\Deperdition;

use App\Domain\Audit\Audit;
use App\Domain\Audit\Engine\ZoneThermique;
use App\Domain\Common\EngineRule;
use App\Domain\Enveloppe\Engine\SurfaceDeperditive\SurfaceDeperditiveEnveloppe;
use App\Domain\Enveloppe\Enum\{EtatIsolation, TypeDeperdition, TypeParoi};
use App\Domain\Enveloppe\Enveloppe;
use App\Domain\Enveloppe\Service\EnveloppeTableValeurRepository;
use App\Domain\Enveloppe\ValueObject\{Deperdition, Permeabilite};
use App\Domain\Ventilation\Engine\PerformanceVentilation;

final class DeperditionRenouvellementAir extends EngineRule
{
    private Audit $audit;
    private Enveloppe $enveloppe;

    public function __construct(private readonly EnveloppeTableValeurRepository $table_repository) {}

    /**
     * @see \App\Domain\Audit\Engine\ZoneThermique::surface_habitable()  
     */
    public function surface_habitable(): float
    {
        return $this->audit->data()->surface_habitable;
    }

    /**
     * @see \App\Domain\Audit\Engine\ZoneThermique::hauteur_sous_plafond()  
     */
    public function hauteur_sous_plafond(): float
    {
        return $this->audit->data()->hauteur_sous_plafond;
    }

    /**
     * @see \App\Domain\Audit\Engine\ZoneThermique::volume_habitable()  
     */
    public function volume_habitable(): float
    {
        return $this->audit->data()->volume_habitable;
    }

    /**
     * @see \App\Domain\Enveloppe\Engine\Deperdition\DeperditionParoi::sdep()
     */
    public function sdep(?EtatIsolation $isolation = null): float
    {
        return $this->enveloppe->data()->surfaces_deperditives->get(isolation: $isolation)
            - $this->enveloppe->data()->surfaces_deperditives->get(type: TypeParoi::PLANCHER_BAS, isolation: $isolation);
    }

    /**
     * Etat d'isolation majoritaire des murs et planchers hauts
     * 
     * @see \App\Domain\Enveloppe\Engine\Deperdition\DeperditionParoi::sdep()
     * @see \App\Domain\Enveloppe\Engine\Deperdition\DeperditionParoi::isolation()
     */
    public function isolation_murs_plafonds(): EtatIsolation
    {
        return $this->sdep(EtatIsolation::ISOLE) > $this->sdep() / 2
            ? EtatIsolation::ISOLE
            : EtatIsolation::NON_ISOLE;
    }

    /**
     * Présence mahoritaire de joints de menuiserie
     */
    public function presence_joints_menuiserie(): bool
    {
        $sdep = 0;
        $sdep_joints = 0;

        foreach ($this->enveloppe->baies() as $paroi) {
            $sdep += $paroi->data()->sdep;
            $sdep_joints += $paroi->menuiserie()->presence_joint ? $paroi->data()->sdep : 0;
        }
        foreach ($this->enveloppe->portes() as $paroi) {
            $sdep += $paroi->data()->sdep;
            $sdep_joints += $paroi->menuiserie()->presence_joint ? $paroi->data()->sdep : 0;
        }
        return $sdep_joints > $sdep / 2;
    }

    /**
     * Débit volumique conventionnel à reprendre exprimé en m3/(h.m²)
     * 
     * @see \App\Domain\Ventilation\Engine\PerformanceVentilation::qvarep_conv()
     */
    public function qvarep_conv(): float
    {
        return $this->audit->ventilation()->data()->qvarep_conv;
    }

    /**
     * Débit volumique conventionnel à souffler exprimé en m3/(h.m²)
     * 
     * @see \App\Domain\Ventilation\Engine\PerformanceVentilation::qvasouf_conv()
     */
    public function qvasouf_conv(): float
    {
        return $this->audit->ventilation()->data()->qvasouf_conv;
    }

    /**
     * Somme des modules d’entrée d'air sous 20 Pa par unité de surface habitable exprimée en m3/(h.m²)
     * 
     * @see \App\Domain\Ventilation\Engine\PerformanceVentilation::smea_conv()
     */
    public function smea_conv(): float
    {
        return $this->audit->ventilation()->data()->smea_conv;
    }

    /**
     * Déperditions thermiques de l'enveloppe par renouvellement d'air exprimées en W/K
     */
    public function dr(): float
    {
        return $this->hvent() + $this->hperm();
    }

    /**
     * Déperdition thermique par renouvellement d’air due au système de ventilation par degré d’écart
     * entre l’intérieur et l’extérieur exprimé en W/K
     */
    public function hvent(): float
    {
        return 0.34 * $this->qvarep_conv() * $this->surface_habitable();
    }

    /**
     * Déperdition thermique par renouvellement d’air due au vent par degré d’écart entre
     * l’intérieur et l’extérieur exprimé en W/K
     */
    public function hperm(): float
    {
        return 0.34 * $this->qvinf();
    }

    /**
     * Débit d’air dû aux infiltrations liées au vent exprimé en m3/h
     */
    public function qvinf(): float
    {
        $volume_habitable = $this->volume_habitable();
        $hauteur_sous_plafond = $this->hauteur_sous_plafond();
        $e = $this->e();
        $f = $this->f();
        $n50 = $this->n50();
        $qvasouf_conv = $this->qvasouf_conv();
        $qvarep_conv = $this->qvarep_conv();

        $qvinf = $volume_habitable * $n50 * $e;
        $qvinf /= 1 + ($f / $e) * \pow(($qvasouf_conv - $qvarep_conv) / ($hauteur_sous_plafond * $n50), 2);
        return $qvinf;
    }

    /**
     * Renouvellement d'air sous 50 Pascals exprimé en h-1
     */
    public function n50(): float
    {
        return $this->q4pa() / (\pow(4 / 50, 2 / 3) * $this->volume_habitable());
    }

    /**
     * Perméabilité sous 4 Pa de la zone exprimée en m3/h
     */
    public function q4pa(): float
    {
        return $this->q4pa_env() + 0.45 * $this->smea_conv() * $this->surface_habitable();
    }

    /**
     * Perméabilité de l'enveloppe exprimée en m3/h
     */
    public function q4pa_env(): float
    {
        return $this->q4pa_conv() * $this->sdep();
    }

    /**
     * Coefficients de protection
     */
    public function e(): float
    {
        return $this->enveloppe->exposition()->e();
    }

    /**
     * Coefficients de protection
     */
    public function f(): float
    {
        return $this->enveloppe->exposition()->f();
    }

    /**
     * Valeur conventionnelle de la perméabilité sous 4Pa en m3/(h.m2)
     */
    public function q4pa_conv(): float
    {
        return $this->get('q4pa_conv', function () {
            if ($this->enveloppe->q4pa_conv()) {
                return $this->enveloppe->q4pa_conv();
            }
            if (null === $q4pa_conv = $this->table_repository->q4pa_conv(
                type_batiment: $this->audit->batiment()->type,
                annee_construction: $this->audit->batiment()->annee_construction,
                presence_joints_menuiserie: $this->presence_joints_menuiserie(),
                isolation_murs_plafonds: $this->isolation_murs_plafonds(),
            )) {
                throw new \DomainException('Valeur forfaitaire Q4PaConv non trouvée');
            }

            return $q4pa_conv;
        });
    }

    public function apply(Audit $entity): void
    {
        $this->audit = $entity;
        $this->enveloppe = $entity->enveloppe();
        $this->clear();

        $entity->enveloppe()->calcule($entity->enveloppe()->data()->with(
            permeabilite: Permeabilite::create(
                presence_joints_menuiserie: $this->presence_joints_menuiserie(),
                isolation_murs_plafonds: $this->isolation_murs_plafonds(),
                hvent: $this->hvent(),
                hperm: $this->hperm(),
                q4pa_conv: $this->q4pa_conv(),
            )
        ));

        $entity->enveloppe()->calcule($entity->enveloppe()->data()->add_deperdition(Deperdition::create(
            type: TypeDeperdition::RENOUVELEMENT_AIR,
            deperdition: $this->dr(),
        )));
    }

    public static function dependencies(): array
    {
        return [
            ZoneThermique::class,
            DeperditionParois::class,
            SurfaceDeperditiveEnveloppe::class,
            PerformanceVentilation::class,
        ];
    }
}
