<?php

namespace App\Database\Opendata\Audit;

use App\Database\Opendata\XMLOpendataRepository;
use App\Domain\Audit\{Audit, AuditRepository};
use App\Domain\Common\Type\Id;

final class XMLAuditRepository implements AuditRepository
{
    public function __construct(
        private XMLOpendataRepository $opendata_repository,
        private XMLAuditTransformer $transformer,
    ) {}

    public function find(Id $id): ?Audit
    {
        return ($xml = $this->opendata_repository->find($id)) ? $this->transformer->transform($xml) : null;
    }
}
