<?php

namespace App\Database\Opendata\Ventilation;

use App\Database\Opendata\XMLElement;
use App\Domain\Common\ValueObject\Id;
use App\Domain\Ventilation\{Ventilation, VentilationRepository};
use App\Serializer\Opendata\XMLVentilationDeserializer;
use App\Services\Observatoire\Observatoire;

final class OpendataVentilationRepository implements VentilationRepository
{
    public function __construct(
        private readonly Observatoire $observatoire,
        private readonly XMLVentilationDeserializer $deserializer,
    ) {}

    public function find(Id $id): ?Ventilation
    {
        if ($content = $this->observatoire->find($id)) {
            $xml = \simplexml_load_string($content, XMLElement::class);
            return $this->deserializer->deserialize($xml);
        }
        return null;
    }
}
