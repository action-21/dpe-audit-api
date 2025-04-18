<?php

namespace App\Domain\Chauffage;

use App\Domain\Audit\Audit;
use App\Domain\Common\SpecificationRule;
use Webmozart\Assert\Assert;

final class ChauffageSpecification extends SpecificationRule
{
    private function validate_generateurs(Audit $entity): void
    {
        foreach ($entity->chauffage()->generateurs() as $generateur) {
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

    private function validate_emetteurs(Audit $entity): void
    {
        foreach ($entity->chauffage()->emetteurs() as $emetteur) {
            Assert::nullOrGreaterThanEq(
                $emetteur->annee_installation()?->value(),
                $entity->batiment()->annee_construction->value(),
            );
        }
    }

    public function validate(Audit $entity): void
    {
        $this->validate_generateurs($entity);
        $this->validate_emetteurs($entity);
    }
}
