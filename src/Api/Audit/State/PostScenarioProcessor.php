<?php

namespace App\Api\Audit\State;

use ApiPlatform\Metadata\Operation;
use App\Api\Audit\Model\Audit;
use ApiPlatform\State\ProcessorInterface;
use App\Api\Audit\CreateAuditHandler;

/**
 * @implements ProcessorInterface<Audit|null>
 */
final class PostScenarioProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly CreateAuditHandler $handler,
    ) {}

    /**
     * @param Audit $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Audit
    {
        $handle = $this->handler;
        $entity = $handle($data);
        return Audit::from($entity);
    }
}
