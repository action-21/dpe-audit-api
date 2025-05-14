<?php

namespace App\Database\Opendata\Ventilation;

use App\Database\Opendata\ObservatoireDPEAuditFinder;
use App\Domain\Common\ValueObject\Id;
use App\Domain\Ventilation\{Ventilation, VentilationRepository};
use App\Serializer\Opendata\XMLVentilationDeserializer;

final class OpendataVentilationRepository implements VentilationRepository
{
    public function __construct(
        private ObservatoireDPEAuditFinder $finder,
        private XMLVentilationDeserializer $deserializer,
    ) {}

    public function find(Id $id): ?Ventilation
    {
        return ($xml = $this->finder->find($id)) ? $this->deserializer->deserialize($xml) : null;
    }
}
