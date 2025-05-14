<?php

namespace App\Database\Opendata\Eclairage;

use App\Database\Opendata\ObservatoireDPEAuditFinder;
use App\Domain\Common\ValueObject\Id;
use App\Domain\Eclairage\{Eclairage, EclairageRepository};
use App\Serializer\Opendata\XMLEclairageDeserializer;

final class OpendataEclairageRepository implements EclairageRepository
{
    public function __construct(
        private ObservatoireDPEAuditFinder $finder,
        private XMLEclairageDeserializer $deserializer
    ) {}

    public function find(Id $id): ?Eclairage
    {
        return ($xml = $this->finder->find($id)) ? $this->deserializer->deserialize($xml) : null;
    }
}
