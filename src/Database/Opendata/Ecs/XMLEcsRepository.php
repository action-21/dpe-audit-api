<?php

namespace App\Database\Opendata\Ecs;

use App\Database\Opendata\XMLOpendataRepository;
use App\Domain\Common\ValueObject\Id;
use App\Domain\Ecs\{Ecs, EcsRepository};

final class XMLEcsRepository implements EcsRepository
{
    public function __construct(
        private XMLOpendataRepository $opendata_repository,
        private XMLEcsDeserializer $deserializer,
    ) {}

    public function find(Id $id): ?Ecs
    {
        return ($xml = $this->opendata_repository->find($id))
            ? $this->deserializer->deserialize($xml)
            : null;
    }
}
