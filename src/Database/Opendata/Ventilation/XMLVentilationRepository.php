<?php

namespace App\Database\Opendata\Ventilation;

use App\Database\Opendata\XMLOpendataRepository;
use App\Domain\Common\ValueObject\Id;
use App\Domain\Ventilation\{Ventilation, VentilationRepository};

final class XMLVentilationRepository implements VentilationRepository
{
    public function __construct(
        private XMLOpendataRepository $opendata_repository,
        private XMLVentilationDeserializer $deserializer,
    ) {}

    public function find(Id $id): ?Ventilation
    {
        return ($xml = $this->opendata_repository->find($id))
            ? $this->deserializer->deserialize($xml)
            : null;
    }
}
