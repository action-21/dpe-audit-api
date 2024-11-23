<?php

namespace App\Domain\Chauffage\Service\MoteurRendement;

use App\Domain\Chauffage\Data\RrRepository;
use App\Domain\Chauffage\Entity\{Emetteur, Systeme};
use App\Domain\Chauffage\Enum\{LabelGenerateur, TypeEmission, TypeGenerateur};

final class MoteurRendementRegulation
{
    public function __construct(private RrRepository $rr_repository) {}

    public function calcule_rendement_regulation(Systeme $entity): ?float
    {
        if ($entity->emetteurs()->count() === 0) {
            return $this->rr(
                type_emission: TypeEmission::from_type_generateur($entity->generateur()->type()),
                type_generateur: $entity->generateur()->type(),
                label_generateur: $entity->generateur()->signaletique()->label,
                reseau_collectif: $entity->generateur()->generateur_collectif(),
                presence_regulation_terminale: $entity->installation()->regulation_terminale()->presence_regulation,
                presence_robinet_thermostatique: null,
            );
        }
        return \array_reduce($entity->emetteurs()->values(), fn(float $carry, Emetteur $item): float => $carry += $this->rr(
            type_emission: $item->type_emission(),
            type_generateur: $entity->generateur()->type(),
            label_generateur: $entity->generateur()->signaletique()->label,
            reseau_collectif: $entity->generateur()->generateur_collectif(),
            presence_regulation_terminale: $entity->installation()->regulation_terminale()->presence_regulation,
            presence_robinet_thermostatique: null,
        ), 0) / $entity->emetteurs()->count();
    }

    public function rr(
        TypeEmission $type_emission,
        TypeGenerateur $type_generateur,
        ?LabelGenerateur $label_generateur,
        ?bool $reseau_collectif,
        ?bool $presence_robinet_thermostatique,
        ?bool $presence_regulation_terminale,
    ): float {
        if (null === $data = $this->rr_repository->find_by(
            type_emission: $type_emission,
            type_generateur: $type_generateur,
            label_generateur: $label_generateur,
            reseau_collectif: $reseau_collectif,
            presence_robinet_thermostatique: $presence_robinet_thermostatique,
            presence_regulation_terminale: $presence_regulation_terminale,
        )) throw new \DomainException("Valeur forfaitaire Rr non trouvée.");

        return $data->rr;
    }
}
