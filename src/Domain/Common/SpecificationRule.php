<?php

namespace App\Domain\Common;

use App\Domain\Audit\Audit;

abstract class SpecificationRule
{
    abstract public function validate(Audit $entity): void;
}
