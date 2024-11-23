<?php

namespace App\Database\Opendata\Ecs;

use App\Database\Opendata\XMLOpendataRepository;
use App\Domain\Common\Type\Id;
use App\Domain\Ecs\{Ecs, EcsRepository};

final class XMLEcsRepository implements EcsRepository
{
    public function __construct(
        private XMLOpendataRepository $opendata_repository,
        private XMLEcsTransformer $transformer
    ) {}

    public function find(Id $audit_id): ?Ecs
    {
        return ($xml = $this->opendata_repository->find($audit_id)) ? $this->transformer->transform($xml) : null;
    }
}
