<?php

namespace App\Domain\Common\Service;

use App\Domain\Audit\Audit;

interface EngineRuleResolver
{
    public function apply(Audit $entity): void;
}
