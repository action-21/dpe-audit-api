<?php

namespace App\Database\Opendata\Production;

use App\Database\Opendata\XMLOpendataRepository;
use App\Domain\Common\ValueObject\Id;
use App\Domain\Production\{Production, ProductionRepository};

final class XMLProductionRepository implements ProductionRepository
{
    public function __construct(
        private XMLOpendataRepository $opendata_repository,
        private XMLProductionTransformer $transformer
    ) {}

    public function find(Id $audit_id): ?Production
    {
        return ($xml = $this->opendata_repository->find($audit_id)) ? $this->transformer->transform($xml) : null;
    }
}
