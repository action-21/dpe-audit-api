<?php

namespace App\Database\Opendata\Refroidissement;

use App\Database\Opendata\XMLOpendataRepository;
use App\Domain\Common\ValueObject\Id;
use App\Domain\Refroidissement\{Refroidissement, RefroidissementRepository};

final class XMLRefroidissementRepository implements RefroidissementRepository
{
    public function __construct(
        private XMLOpendataRepository $opendata_repository,
        private XMLRefroidissementDeserializer $deserializer,
    ) {}

    public function find(Id $id): ?Refroidissement
    {
        return ($xml = $this->opendata_repository->find($id))
            ? $this->deserializer->deserialize($xml)
            : null;
    }
}
