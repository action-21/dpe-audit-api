<?php

namespace App\Domain\Enveloppe\Engine\Deperdition;

use App\Domain\Audit\Audit;
use App\Domain\Common\ValueObject\Annee;
use App\Domain\Enveloppe\Entity\Mur;
use App\Domain\Enveloppe\Enum\{EtatIsolation, Performance, TypeDeperdition};
use App\Domain\Enveloppe\Service\MurTableValeurRepository;
use App\Domain\Enveloppe\ValueObject\Deperdition;

/**
 * @property Mur $paroi
 */
final class DeperditionMur extends DeperditionParoi
{
    // Lambda par défaut des murs isolés
    final public const LAMBDA_ISOLATION_DEFAUT = 0.04;

    // Résistance additionnelle dûe à la présence d'un enduit sur une paroi ancienne
    final public const RESISTANCE_ENDUIT_PAROI_ANCIENNE = 0.7;

    public function __construct(private readonly MurTableValeurRepository $table_repository,)
    {
        $this->table_paroi_repository = $table_repository;
    }

    public function annee_construction_isolation(): Annee
    {
        if ($this->paroi->isolation()->annee_isolation) {
            return $this->paroi->isolation()->annee_isolation;
        }
        if ($this->paroi->isolation()->etat_isolation !== EtatIsolation::ISOLE) {
            return $this->paroi->annee_construction();
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
     * Epaisseur de mur par défaut
     */
    public function epaisseur_structure(): ?float
    {
        return $this->paroi->epaisseur_structure() ?? $this->paroi->type_structure()?->epaisseur_structure();
    }

    /**
     * Coefficient de transmission thermique du mur non isolé exprimé en W/m².K
     */
    public function u0(): float
    {
        return $this->get('u0', function () {
            if ($this->paroi->u0()) {
                return $this->paroi->u0();
            }
            if (null === $u0 = $this->table_repository->u0(
                type_structure: $this->paroi->type_structure(),
                epaisseur_structure: $this->epaisseur_structure(),
                annee_construction: $this->annee_construction(),
            )) {
                throw new \DomainException('Valeur forfaitaire Umur non trouvée');
            }

            $u0 += $this->u0_doublage();
            $u0 += $this->u0_enduit_isolant();

            return \min($u0, 2.5);
        });
    }

    /**
     * Coefficient de transmission thermique additionnel dû à la présence d'un enduit isolant
     * sur une paroi ancienne exprimé en W/m².K
     */
    public function u0_enduit_isolant(): float
    {
        if (null === $paroi_ancienne = $this->paroi->paroi_ancienne()) {
            return 0;
        }
        if (null === $presence_enduit_isolant = $this->paroi->presence_enduit_isolant()) {
            return 0;
        }
        return $paroi_ancienne && $presence_enduit_isolant
            ? 1 / self::RESISTANCE_ENDUIT_PAROI_ANCIENNE
            : 0;
    }

    /**
     * Coefficient de transmission thermique additionnel dû au doublage exprimé en W/m².K
     */
    public function u0_doublage(): float
    {
        return ($r_doublage = $this->paroi->type_doublage()?->resistance_thermique_doublage()) > 0
            ? 1 / $r_doublage
            : 0;
    }

    /**
     * Coefficient de transmission thermique du mur isolé exprimé en W/m².K
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
                zone_climatique: $this->audit->adresse()->zone_climatique,
                effet_joule: $this->audit->data()->effet_joule,
                annee_construction_isolation: $this->annee_construction_isolation(),
            )) {
                throw new \DomainException('Valeur forfaitaire Umur non trouvée');
            }
            return \min($this->u0(), $u);
        });
    }

    /**
     * Etat de performance du mur
     */
    public function performance(): Performance
    {
        $u = $this->u();

        return match (true) {
            $u >= 0.65 => Performance::INSUFFISANTE,
            $u >= 0.45 => Performance::MOYENNE,
            $u >= 0.3 => Performance::BONNE,
            $u < 0.3 => Performance::TRES_BONNE,
        };
    }

    public function apply(Audit $entity): void
    {
        $this->audit = $entity;

        foreach ($entity->enveloppe()->murs() as $paroi) {
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
                type: TypeDeperdition::MUR,
                deperdition: $this->dp(),
            )));
        }
    }
}
