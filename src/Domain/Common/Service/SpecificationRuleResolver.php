<?php

namespace App\Domain\Common\Service;

use App\Domain\Audit\Audit;

interface SpecificationRuleResolver
{
    public function apply(Audit $entity): void;

    public function notifications(): array;
}
