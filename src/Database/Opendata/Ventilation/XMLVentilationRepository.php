<?php

namespace App\Database\Opendata\Ventilation;

use App\Database\Opendata\XMLOpendataRepository;
use App\Domain\Common\ValueObject\Id;
use App\Domain\Ventilation\{Ventilation, VentilationRepository};

final class XMLVentilationRepository implements VentilationRepository
{
    public function __construct(
        private XMLOpendataRepository $opendata_repository,
        private XMLVentilationTransformer $transformer
    ) {}

    public function find(Id $audit_id): ?Ventilation
    {
        return ($xml = $this->opendata_repository->find($audit_id)) ? $this->transformer->transform($xml) : null;
    }
}
