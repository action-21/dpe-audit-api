<?php

namespace App\Engine\Performance\Deperdition;

use App\Domain\Audit\Audit;
use App\Domain\Common\ValueObject\Annee;
use App\Domain\Enveloppe\Entity\PlancherHaut;
use App\Domain\Enveloppe\Enum\Lnc\TypeLnc;
use App\Domain\Enveloppe\Enum\{EtatIsolation, Performance, TypeDeperdition};
use App\Domain\Enveloppe\Enum\PlancherHaut\Configuration;
use App\Domain\Enveloppe\Service\PlancherHautTableValeurRepository;
use App\Domain\Enveloppe\ValueObject\Deperdition;

/**
 * @extends DeperditionParoi<PlancherHaut>
 */
final class DeperditionPlancherHaut extends DeperditionParoi
{
    // Lambda par défaut des planchers hauts isolés
    final public const LAMBDA_ISOLATION_DEFAUT = 0.04;

    public function __construct(private PlancherHautTableValeurRepository $table_repository,)
    {
        $this->table_paroi_repository = $table_repository;
    }

    public function configuration(): Configuration
    {
        if ($this->paroi->configuration()) {
            return $this->paroi->configuration();
        }
        if ($this->paroi->type_structure()) {
            return Configuration::from_type_plancher_haut(
                $this->paroi->type_structure()
            );
        }
        return match ($this->paroi->local_non_chauffe()?->type()) {
            TypeLnc::COMBLE_FORTEMENT_VENTILE => Configuration::COMBLES_PERDUS,
            TypeLnc::COMBLE_FAIBLEMENT_VENTILE => Configuration::COMBLES_PERDUS,
            TypeLnc::COMBLE_TRES_FAIBLEMENT_VENTILE => Configuration::COMBLES_PERDUS,
            default => Configuration::TERRASSE,
        };
    }

    public function annee_construction_isolation(): Annee
    {
        if ($this->paroi->isolation()->annee_isolation) {
            return $this->paroi->isolation()->annee_isolation;
        }
        if ($this->paroi->isolation()->etat_isolation !== EtatIsolation::ISOLE) {
            return $this->annee_construction();
        }
        $annee_construction = $this->annee_construction();
        return $annee_construction->less_than(1975) ? Annee::from(1975) : $annee_construction;
    }

    public function annee_construction(): Annee
    {
        return current(array_filter([
            $this->paroi->annee_renovation(),
            $this->paroi->annee_construction(),
            $this->audit->batiment()->annee_construction,
        ]));
    }

    /** @inheritdoc */
    public function sdep(): float
    {
        return $this->paroi->data()->sdep;
    }

    /** @inheritdoc */
    public function isolation(): EtatIsolation
    {
        return $this->paroi->data()->isolation;
    }

    /**
     * Coefficient de transmission thermique du plancher haut non isolé exprimé en W/m².K
     */
    public function u0(): float
    {
        return $this->get('u0', function () {
            if ($this->paroi->u0()) {
                return $this->paroi->u0();
            }
            if (null === $value = $this->table_repository->u0(
                type_structure: $this->paroi->type_structure(),
            )) {
                throw new \DomainException('Valeur forfaitaire U0 non trouvée');
            }
            return \min($value, 2.5);
        });
    }

    /**
     * Coefficient de transmission thermique du plancher haut isolé exprimé en W/m².K
     */
    public function u(): float
    {
        return $this->get('u', function () {
            if ($this->paroi->u()) {
                return $this->paroi->u();
            }
            if ($this->paroi->isolation()->etat_isolation === EtatIsolation::NON_ISOLE) {
                return $this->u0();
            }
            if ($this->paroi->isolation()->etat_isolation === EtatIsolation::ISOLE) {
                if ($r = $this->paroi->isolation()->resistance_thermique_isolation) {
                    return 1 / (1 / $this->u0() + $r);
                }
                if ($e = $this->paroi->isolation()->epaisseur_isolation) {
                    return 1 / (1 / $this->u0() + $e / 1000 / self::LAMBDA_ISOLATION_DEFAUT);
                }
            }
            if (null === $u = $this->table_repository->u(
                zone_climatique: $this->zone_climatique(),
                configuration: $this->configuration(),
                effet_joule: $this->audit->data()->effet_joule,
                annee_construction_isolation: $this->annee_construction_isolation(),
            )) {
                throw new \DomainException('Valeur forfaitaire Uph non trouvée');
            }
            return \min($this->u0(), $u);
        });
    }

    /**
     * Etat de performance du plancher haut
     */
    public function performance(): Performance
    {
        $u = $this->u();

        return match ($this->configuration()) {
            Configuration::COMBLES_PERDUS => match (true) {
                $u >= 0.3 => Performance::INSUFFISANTE,
                $u >= 0.2 => Performance::MOYENNE,
                $u >= 0.15 => Performance::BONNE,
                $u < 0.15 => Performance::TRES_BONNE,
            },
            Configuration::RAMPANTS => match (true) {
                $u >= 0.3 => Performance::INSUFFISANTE,
                $u >= 0.25 => Performance::MOYENNE,
                $u >= 0.18 => Performance::BONNE,
                $u < 0.18 => Performance::TRES_BONNE,
            },
            Configuration::TERRASSE => match (true) {
                $u >= 0.35 => Performance::INSUFFISANTE,
                $u >= 0.3 => Performance::MOYENNE,
                $u >= 0.25 => Performance::BONNE,
                $u < 0.25 => Performance::TRES_BONNE,
            },
        };
    }

    public function apply(Audit $entity): void
    {
        $this->audit = $entity;

        foreach ($entity->enveloppe()->planchers_hauts() as $paroi) {
            $this->paroi = $paroi;
            $this->clear();

            $paroi->calcule($paroi->data()->with(
                sdep: $this->sdep(),
                b: $this->b(),
                u0: $this->u0(),
                u: $this->u(),
                dp: $this->dp(),
                performance: $this->performance(),
            ));

            $entity->enveloppe()->calcule($entity->enveloppe()->data()->add_deperdition(Deperdition::create(
                type: TypeDeperdition::PLANCHER_HAUT,
                deperdition: $this->dp(),
            )));
        }
    }
}
