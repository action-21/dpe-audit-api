<?php

namespace App\Domain\Enveloppe\Engine\Deperdition;

use App\Domain\Audit\Audit;
use App\Domain\Enveloppe\Entity\Porte;
use App\Domain\Enveloppe\Enum\{EtatIsolation, Performance, TypeDeperdition};
use App\Domain\Enveloppe\Enum\Porte\{Materiau, TypeVitrage};
use App\Domain\Enveloppe\Service\PorteTableValeurRepository;
use App\Domain\Enveloppe\ValueObject\Deperdition;

/**
 * @property Porte $paroi
 */
final class DeperditionPorte extends DeperditionParoi
{
    public function __construct(private readonly PorteTableValeurRepository $table_repository,)
    {
        $this->table_paroi_repository = $table_repository;
    }

    public function materiau(): Materiau
    {
        return $this->paroi->materiau() ?? Materiau::PVC;
    }

    public function type_vitrage(): ?TypeVitrage
    {
        if ($this->paroi->vitrage()->type_vitrage) {
            return $this->paroi->vitrage()->type_vitrage;
        }
        if (0 === $this->paroi->vitrage()->taux_vitrage) {
            return null;
        }
        return TypeVitrage::SIMPLE_VITRAGE;
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
     * Coefficient de transmission thermique exprimé en W/m².KK
     */
    public function u(): float
    {
        return $this->get('u', function () {
            if ($this->paroi->u()) {
                return $this->paroi->u();
            }
            if (null === $value = $this->table_repository->u(
                presence_sas: $this->paroi->presence_sas(),
                isolation: $this->isolation(),
                materiau: $this->materiau(),
                type_vitrage: $this->type_vitrage(),
                taux_vitrage: $this->paroi->vitrage()->taux_vitrage->value(),
            )) {
                throw new \DomainException('Valeur forfaitaire Uporte non trouvée');
            }
            return $value;
        });
    }

    /**
     * Etat de performance de la porte
     * 
     * @see Arrêté du 31 mars 2021 relatif au diagnostic de performance énergétique
     * pour les bâtiments ou parties de bâtiments à usage d'habitation en France métropolitaine
     */
    public function performance(): Performance
    {
        $u = $this->u();
        return match (true) {
            $u >= 3 => Performance::INSUFFISANTE,
            $u >= 2.2 => Performance::MOYENNE,
            $u >= 1.6 => Performance::BONNE,
            $u < 1.6 => Performance::TRES_BONNE,
        };
    }

    public function apply(Audit $entity): void
    {
        $this->audit = $entity;

        foreach ($entity->enveloppe()->portes() as $paroi) {
            $this->paroi = $paroi;
            $this->clear();

            $paroi->calcule($paroi->data()->with(
                sdep: $this->sdep(),
                b: $this->b(),
                u: $this->u(),
                dp: $this->dp(),
                performance: $this->performance(),
            ));

            $entity->enveloppe()->calcule($entity->enveloppe()->data()->add_deperdition(Deperdition::create(
                type: TypeDeperdition::PORTE,
                deperdition: $this->dp(),
            )));
        }
    }
}
