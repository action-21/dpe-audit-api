<?php

namespace App\Database\Opendata\Eclairage;

use App\Database\Opendata\XMLElement;
use App\Domain\Common\ValueObject\Id;
use App\Domain\Eclairage\{Eclairage, EclairageRepository};
use App\Serializer\Opendata\XMLEclairageDeserializer;
use App\Services\Observatoire\Observatoire;

final class OpendataEclairageRepository implements EclairageRepository
{
    public function __construct(
        private readonly Observatoire $observatoire,
        private readonly XMLEclairageDeserializer $deserializer
    ) {}

    public function find(Id $id): ?Eclairage
    {
        if ($content = $this->observatoire->find($id)) {
            $xml = \simplexml_load_string($content, XMLElement::class);
            return $this->deserializer->deserialize($xml);
        }
        return null;
    }
}
