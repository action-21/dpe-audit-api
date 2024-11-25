<?php

namespace App\Api\Ventilation\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

final class PostVentilationProcessor implements ProcessorInterface
{
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []) {}
}
