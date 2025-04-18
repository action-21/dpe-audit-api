<?php

namespace App\Database\Opendata\Chauffage;

use App\Database\Opendata\XMLOpendataRepository;
use App\Domain\Common\ValueObject\Id;
use App\Domain\Chauffage\{Chauffage, ChauffageRepository};

final class XMLChauffageRepository implements ChauffageRepository
{
    public function __construct(
        private XMLOpendataRepository $opendata_repository,
        private XMLChauffageDeserializer $deserializer
    ) {}

    public function find(Id $id): ?Chauffage
    {
        return ($xml = $this->opendata_repository->find($id))
            ? $this->deserializer->deserialize($xml)
            : null;
    }
}
