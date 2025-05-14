<?php

namespace App\Database\Opendata\Production;

use App\Database\Opendata\ObservatoireDPEAuditFinder;
use App\Domain\Common\ValueObject\Id;
use App\Domain\Production\{Production, ProductionRepository};
use App\Serializer\Opendata\XMLProductionDeserializer;

final class OpendataProductionRepository implements ProductionRepository
{
    public function __construct(
        private ObservatoireDPEAuditFinder $finder,
        private XMLProductionDeserializer $deserializer,
    ) {}

    public function find(Id $id): ?Production
    {
        return ($xml = $this->finder->find($id)) ? $this->deserializer->deserialize($xml) : null;
    }
}
