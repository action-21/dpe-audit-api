<?php

namespace App\Database\Opendata\Eclairage;

use App\Database\Opendata\XMLOpendataRepository;
use App\Domain\Common\ValueObject\Id;
use App\Domain\Eclairage\{Eclairage, EclairageRepository};

final class XMLEclairageRepository implements EclairageRepository
{
    public function __construct(
        private XMLOpendataRepository $opendata_repository,
        private XMLEclairageTransformer $transformer
    ) {}

    public function find(Id $audit_id): ?Eclairage
    {
        return ($xml = $this->opendata_repository->find($audit_id)) ? $this->transformer->transform($xml) : null;
    }
}
