<?php

namespace App\Database\Opendata\Ecs;

use App\Database\Opendata\ObservatoireDPEAuditFinder;
use App\Database\Opendata\XMLElement;
use App\Domain\Common\ValueObject\Id;
use App\Domain\Ecs\{Ecs, EcsRepository};
use App\Serializer\Opendata\XMLEcsDeserializer;
use App\Services\Observatoire\Observatoire;

final class OpendataEcsRepository implements EcsRepository
{
    public function __construct(
        private readonly Observatoire $observatoire,
        private readonly XMLEcsDeserializer $deserializer,
    ) {}

    public function find(Id $id): ?Ecs
    {
        if ($content = $this->observatoire->find($id)) {
            $xml = \simplexml_load_string($content, XMLElement::class);
            return $this->deserializer->deserialize($xml);
        }
        return null;
    }
}
