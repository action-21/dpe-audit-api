<?php

namespace App\Database\Opendata\Refroidissement;

use App\Database\Opendata\XMLElement;
use App\Domain\Common\ValueObject\Id;
use App\Domain\Refroidissement\{Refroidissement, RefroidissementRepository};
use App\Serializer\Opendata\XMLRefroidissementDeserializer;
use App\Services\Observatoire\Observatoire;

final class OpendataRefroidissementRepository implements RefroidissementRepository
{
    public function __construct(
        private readonly Observatoire $observatoire,
        private readonly XMLRefroidissementDeserializer $deserializer,
    ) {}

    public function find(Id $id): ?Refroidissement
    {
        if ($content = $this->observatoire->find($id)) {
            $xml = \simplexml_load_string($content, XMLElement::class);
            return $this->deserializer->deserialize($xml);
        }
        return null;
    }
}
