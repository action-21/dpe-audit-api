<?php

namespace App\Database\Opendata\Enveloppe;

use App\Database\Opendata\ObservatoireDPEAuditFinder;
use App\Domain\Common\ValueObject\Id;
use App\Domain\Enveloppe\{Enveloppe, EnveloppeRepository};
use App\Serializer\Opendata\XMLEnveloppeDeserializer;

final class OpendataEnveloppeRepository implements EnveloppeRepository
{
    public function __construct(
        private ObservatoireDPEAuditFinder $finder,
        private XMLEnveloppeDeserializer $deserializer
    ) {}

    public function find(Id $id): ?Enveloppe
    {
        return ($xml = $this->finder->find($id)) ? $this->deserializer->deserialize($xml) : null;
    }
}
