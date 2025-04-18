<?php

namespace App\Api\Audit;

use App\Domain\Audit\Audit;
use App\Domain\Common\EngineRule;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

/**
 * @property EngineRule[] $rules
 */
final class ComputeAuditHandler
{
    public function __construct(
        #[AutowireIterator('app.engine_rule', defaultPriorityMethod: 'priority')]
        private readonly iterable $rules,
    ) {}

    public function __invoke(Audit $entity): Audit
    {
        foreach ($this->rules as $rule) {
            $rule->apply($entity);
        }
        return $entity;
    }
}
