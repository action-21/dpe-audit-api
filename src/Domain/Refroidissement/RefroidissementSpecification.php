<?php

namespace App\Domain\Refroidissement;

use App\Domain\Audit\Audit;
use App\Domain\Common\SpecificationRule;
use Webmozart\Assert\Assert;

final class RefroidissementSpecification extends SpecificationRule
{
    private function validate_generateurs(Audit $entity): void
    {
        foreach ($entity->refroidissement()->generateurs() as $generateur) {
            Assert::nullOrGreaterThanEq(
                $generateur->annee_installation()?->value(),
                $entity->batiment()->annee_construction->value(),
            );
        }
    }

    public function validate(Audit $entity): void
    {
        $this->validate_generateurs($entity);
    }
}
