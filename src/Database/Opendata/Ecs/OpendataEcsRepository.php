<?php

namespace App\Database\Opendata\Ecs;

use App\Database\Opendata\ObservatoireDPEAuditFinder;
use App\Domain\Common\ValueObject\Id;
use App\Domain\Ecs\{Ecs, EcsRepository};
use App\Serializer\Opendata\XMLEcsDeserializer;

final class OpendataEcsRepository implements EcsRepository
{
    public function __construct(
        private ObservatoireDPEAuditFinder $finder,
        private XMLEcsDeserializer $deserializer,
    ) {}

    public function find(Id $id): ?Ecs
    {
        return ($xml = $this->finder->find($id)) ? $this->deserializer->deserialize($xml) : null;
    }
}
