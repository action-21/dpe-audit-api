<?php

namespace App\Database\Opendata\Visite;

use App\Database\Opendata\XMLOpendataRepository;
use App\Domain\Common\ValueObject\Id;
use App\Domain\Visite\{Visite, VisiteRepository};

final class XMLVisiteRepository implements VisiteRepository
{
    public function __construct(
        private XMLOpendataRepository $opendata_repository,
        private XMLVisiteTransformer $transformer
    ) {}

    public function find(Id $audit_id): ?Visite
    {
        return ($xml = $this->opendata_repository->find($audit_id)) ? $this->transformer->transform($xml) : null;
    }
}
