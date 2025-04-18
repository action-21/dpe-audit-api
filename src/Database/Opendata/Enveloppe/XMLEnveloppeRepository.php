<?php

namespace App\Database\Opendata\Enveloppe;

use App\Database\Opendata\XMLOpendataRepository;
use App\Domain\Common\ValueObject\Id;
use App\Domain\Enveloppe\{Enveloppe, EnveloppeRepository};

final class XMLEnveloppeRepository implements EnveloppeRepository
{
    public function __construct(
        private XMLOpendataRepository $opendata_repository,
        private XMLEnveloppeDeserializer $deserializer
    ) {}

    public function find(Id $id): ?Enveloppe
    {
        return ($xml = $this->opendata_repository->find($id))
            ? $this->deserializer->deserialize($xml)
            : null;
    }
}
