<?php

namespace App\Database\Opendata\Refroidissement;

use App\Database\Opendata\XMLOpendataRepository;
use App\Domain\Common\Type\Id;
use App\Domain\Refroidissement\{Refroidissement, RefroidissementRepository};

final class XMLRefroidissementRepository implements RefroidissementRepository
{
    public function __construct(
        private XMLOpendataRepository $opendata_repository,
        private XMLRefroidissementTransformer $transformer
    ) {}

    public function find(Id $audit_id): ?Refroidissement
    {
        return ($xml = $this->opendata_repository->find($audit_id)) ? $this->transformer->transform($xml) : null;
    }
}
