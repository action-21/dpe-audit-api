<?php

namespace App\Engine\Performance;

use App\Domain\Audit\Audit;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

/**
 * @property RuleInterface[] $rules
 */
final class Engine
{
    public function __construct(
        #[AutowireIterator('app.engine.performance.rule', defaultPriorityMethod: 'priority')]
        private readonly iterable $rules,
    ) {}

    public function __invoke(Audit $entity): Audit
    {
        foreach ($this->rules as $rule) {
            //$time = new \DateTime;
            $rule->apply($entity);
            //$duration = (new \DateTime)->diff($time);
            //echo $rule::class . '|' . $duration->f . "\n";
        }
        return $entity;
    }
}
