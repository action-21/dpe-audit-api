<?php

namespace App\Database\Opendata\Production;

use App\Database\Opendata\XMLElement;
use App\Domain\Common\ValueObject\Id;
use App\Domain\Production\{Production, ProductionRepository};
use App\Serializer\Opendata\XMLProductionDeserializer;
use App\Services\Observatoire\Observatoire;

final class OpendataProductionRepository implements ProductionRepository
{
    public function __construct(
        private readonly Observatoire $observatoire,
        private readonly XMLProductionDeserializer $deserializer,
    ) {}

    public function find(Id $id): ?Production
    {
        if ($content = $this->observatoire->find($id)) {
            $xml = \simplexml_load_string($content, XMLElement::class);
            return $this->deserializer->deserialize($xml);
        }
        return null;
    }
}
