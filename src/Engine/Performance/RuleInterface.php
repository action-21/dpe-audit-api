<?php

namespace App\Engine\Performance;

use App\Domain\Audit\Audit;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.engine.performance.rule')]
interface RuleInterface
{
    public function apply(Audit $entity): void;
    public static function supports(Audit $entity): bool;
    public static function priority(): int;
}
