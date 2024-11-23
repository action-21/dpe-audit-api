<?php

namespace App\Database\Opendata\Enveloppe;

use App\Database\Opendata\XMLOpendataRepository;
use App\Domain\Common\Type\Id;
use App\Domain\Enveloppe\{Enveloppe, EnveloppeRepository};

final class XMLEnveloppeRepository implements EnveloppeRepository
{
    public function __construct(
        private XMLOpendataRepository $opendata_repository,
        private XMLEnveloppeTransformer $transformer
    ) {}

    public function find(Id $audit_id): ?Enveloppe
    {
        return ($xml = $this->opendata_repository->find($audit_id)) ? $this->transformer->transform($xml) : null;
    }
}
