<?php

namespace App\Database\Opendata\Chauffage;

use App\Database\Opendata\ObservatoireDPEAuditFinder;
use App\Domain\Common\ValueObject\Id;
use App\Domain\Chauffage\{Chauffage, ChauffageRepository};
use App\Serializer\Opendata\XMLChauffageDeserializer;

final class OpendataChauffageRepository implements ChauffageRepository
{
    public function __construct(
        private ObservatoireDPEAuditFinder $finder,
        private XMLChauffageDeserializer $deserializer
    ) {}

    public function find(Id $id): ?Chauffage
    {
        return ($xml = $this->finder->find($id)) ? $this->deserializer->deserialize($xml) : null;
    }
}
