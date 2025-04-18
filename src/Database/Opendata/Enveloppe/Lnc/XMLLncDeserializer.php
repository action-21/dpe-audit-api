<?php

namespace App\Database\Opendata\Enveloppe\Lnc;

use App\Database\Opendata\XMLElement;
use App\Domain\Enveloppe\Entity\Lnc;
use App\Domain\Enveloppe\Enveloppe;

final class XMLLncDeserializer
{
    public function __construct(
        private readonly XMLEtsDeserializer $ets_deserializer,
        private readonly XMLLncVirtuelDeserializer $lnc_virtuel_deserializer,
    ) {}

    public function deserialize(XMLElement $xml, Enveloppe $entity): Lnc
    {
        return match ($xml->getName()) {
            'ets' => $this->ets_deserializer->deserialize($xml, $entity),
            default => $this->lnc_virtuel_deserializer->deserialize($xml, $entity),
        };
    }
}
