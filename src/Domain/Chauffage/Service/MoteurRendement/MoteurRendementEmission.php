<?php

namespace App\Domain\Chauffage\Service\MoteurRendement;

use App\Domain\Chauffage\Data\ReRepository;
use App\Domain\Chauffage\Entity\{Emetteur, Systeme};
use App\Domain\Chauffage\Enum\{LabelGenerateur, TypeEmission, TypeGenerateur};

final class MoteurRendementEmission
{
    public function __construct(private ReRepository $re_repository,) {}

    public function calcule_rendement_emission(Systeme $entity): ?float
    {
        if ($entity->emetteurs()->count() === 0) {
            return $this->re(
                type_emission: TypeEmission::from_type_generateur($entity->generateur()->type()),
                type_generateur: $entity->generateur()->type(),
                label_generateur: $entity->generateur()->signaletique()->label,
            );
        }
        return \array_reduce($entity->emetteurs()->values(), fn(float $carry, Emetteur $item): float => $carry += $this->re(
            type_emission: $item->type_emission(),
            type_generateur: $entity->generateur()->type(),
            label_generateur: $entity->generateur()->signaletique()->label,
        ), 0) / $entity->emetteurs()->count();
    }

    public function re(
        TypeEmission $type_emission,
        TypeGenerateur $type_generateur,
        ?LabelGenerateur $label_generateur,
    ): float {
        if (null === $data = $this->re_repository->find_by(
            type_emission: $type_emission,
            type_generateur: $type_generateur,
            label_generateur: $label_generateur,
        )) throw new \DomainException("Valeur forfaitaire Re non trouvée.");

        return $data->re;
    }
}
