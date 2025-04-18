<?php

namespace App\Domain\Ecs;

use App\Domain\Audit\Audit;
use App\Domain\Common\SpecificationRule;
use Webmozart\Assert\Assert;

final class EcsSpecification extends SpecificationRule
{
    private function validate_generateurs(Audit $entity): void
    {
        foreach ($entity->ecs()->generateurs() as $generateur) {
            Assert::nullOrGreaterThanEq(
                $generateur->annee_installation()?->value(),
                $entity->batiment()->annee_construction->value(),
            );
            Assert::nullOrNotEmpty(
                $generateur->position()->generateur_mixte_id,
                $entity->chauffage()->generateurs()->find($generateur->position()->generateur_mixte_id),
            );
        }
    }

    private function validate_installations(Audit $entity): void
    {
        foreach ($entity->ecs()->installations() as $installation) {
            Assert::nullOrGreaterThanEq(
                $installation->solaire_thermique()?->annee_installation?->value(),
                $entity->batiment()->annee_construction->value(),
            );
        }
    }

    public function validate(Audit $entity): void
    {
        $this->validate_installations($entity);
        $this->validate_generateurs($entity);
    }
}
