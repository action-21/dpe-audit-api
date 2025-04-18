<?php

namespace App\Domain\Enveloppe\Engine\Deperdition;

use App\Domain\Audit\Audit;
use App\Domain\Common\ValueObject\Annee;
use App\Domain\Enveloppe\Entity\PlancherBas;
use App\Domain\Enveloppe\Enum\{EtatIsolation, Mitoyennete, Performance, TypeDeperdition};
use App\Domain\Enveloppe\Service\PlancherBasTableValeurRepository;
use App\Domain\Enveloppe\ValueObject\Deperdition;

/**
 * @property PlancherBas $paroi
 */
final class DeperditionPlancherBas extends DeperditionParoi
{
    // Lambda par défaut des planchers bas isolés
    final public const LAMBDA_ISOLATION_DEFAUT = 0.042;

    public function __construct(private PlancherBasTableValeurRepository $table_repository,)
    {
        $this->table_paroi_repository = $table_repository;
    }

    public function annee_construction(): Annee
    {
        return current(array_filter([
            $this->paroi->annee_renovation(),
            $this->paroi->annee_construction(),
            $this->audit->batiment()->annee_construction,
        ]));
    }

    public function annee_construction_isolation(): Annee
    {
        if ($this->paroi->isolation()->annee_isolation) {
            return $this->paroi->isolation()->annee_isolation;
        }
        $annee_construction = $this->annee_construction();
        return $annee_construction->less_than(1975) ? Annee::from(1975) : $annee_construction;
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
     * Coefficient de transmission thermique du plancher bas non isolé exprimé en W/m².K
     */
    public function u0(): float
    {
        if ($this->paroi->u0()) {
            return $this->paroi->u0();
        }
        if (null === $value = $this->table_repository->u0(
            type_structure: $this->paroi->type_structure(),
        )) {
            throw new \DomainException('Valeur forfaitaire U0 non trouvée');
        }
        return $value;
    }

    /**
     * Coefficient de transmission thermique du plancher bas isolé exprimé en W/m².K
     */
    public function u(): float
    {
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
            throw new \DomainException('Valeur forfaitaire Upb non trouvée');
        }
        return \min($this->u0(), $u);
    }

    /**
     * Coefficient de transmission thermique du plancher bas isolé exprimé en W/m².K
     */
    public function u_final(): float
    {
        $u = $this->u();

        $u_final = \in_array($this->paroi->mitoyennete(), [
            Mitoyennete::TERRE_PLEIN,
            Mitoyennete::VIDE_SANITAIRE,
            Mitoyennete::SOUS_SOL_NON_CHAUFFE
        ]) ? $this->table_repository->ue(
            mitoyennete: $this->paroi->mitoyennete(),
            annee_construction: $this->annee_construction(),
            surface: $this->paroi->position()->surface,
            perimetre: $this->paroi->position()->perimetre,
            u: $u,
        ) : $u;

        return \min($u, $u_final);
    }

    /**
     * Etat de performance du plancher bas
     */
    public function performance(): Performance
    {
        $u = $this->u();

        return match (true) {
            $u >= 0.65 => Performance::INSUFFISANTE,
            $u >= 0.45 => Performance::MOYENNE,
            $u >= 0.25 => Performance::BONNE,
            $u < 0.25 => Performance::TRES_BONNE,
        };
    }

    public function apply(Audit $entity): void
    {
        $this->audit = $entity;

        foreach ($entity->enveloppe()->planchers_bas() as $paroi) {
            $this->paroi = $paroi;

            $paroi->calcule($paroi->data()->with(
                sdep: $this->sdep(),
                b: $this->b(),
                u0: $this->u0(),
                u: $this->u_final(),
                performance: $this->performance(),
            ));

            $entity->enveloppe()->calcule($entity->enveloppe()->data()->add_deperdition(Deperdition::create(
                type: TypeDeperdition::PLANCHER_BAS,
                deperdition: $this->dp(),
            )));
        }
    }
}
