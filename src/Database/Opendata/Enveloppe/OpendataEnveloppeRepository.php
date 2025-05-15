<?php

namespace App\Database\Opendata\Enveloppe;

use App\Database\Opendata\XMLElement;
use App\Domain\Common\ValueObject\Id;
use App\Domain\Enveloppe\{Enveloppe, EnveloppeRepository};
use App\Serializer\Opendata\XMLEnveloppeDeserializer;
use App\Services\Observatoire\Observatoire;

final class OpendataEnveloppeRepository implements EnveloppeRepository
{
    public function __construct(
        private readonly Observatoire $observatoire,
        private readonly XMLEnveloppeDeserializer $deserializer
    ) {}

    public function find(Id $id): ?Enveloppe
    {
        if ($content = $this->observatoire->find($id)) {
            $xml = \simplexml_load_string($content, XMLElement::class);
            return $this->deserializer->deserialize($xml);
        }
        return null;
    }
}
