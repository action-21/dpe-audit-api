<?php

namespace App\Database\Opendata\Refroidissement;

use App\Database\Opendata\ObservatoireDPEAuditFinder;
use App\Domain\Common\ValueObject\Id;
use App\Domain\Refroidissement\{Refroidissement, RefroidissementRepository};
use App\Serializer\Opendata\XMLRefroidissementDeserializer;

final class OpendataRefroidissementRepository implements RefroidissementRepository
{
    public function __construct(
        private ObservatoireDPEAuditFinder $finder,
        private XMLRefroidissementDeserializer $deserializer,
    ) {}

    public function find(Id $id): ?Refroidissement
    {
        return ($xml = $this->finder->find($id)) ? $this->deserializer->deserialize($xml) : null;
    }
}
