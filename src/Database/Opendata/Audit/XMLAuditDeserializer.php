<?php

namespace App\Database\Opendata\Audit;

use App\Database\Opendata\Chauffage\XMLChauffageDeserializer;
use App\Database\Opendata\Eclairage\XMLEclairageDeserializer;
use App\Database\Opendata\Ecs\XMLEcsDeserializer;
use App\Database\Opendata\Enveloppe\XMLEnveloppeDeserializer;
use App\Database\Opendata\Production\XMLProductionDeserializer;
use App\Database\Opendata\Refroidissement\XMLRefroidissementDeserializer;
use App\Database\Opendata\Ventilation\XMLVentilationDeserializer;
use App\Database\Opendata\XMLElement;
use App\Domain\Audit\{Audit, AuditData};
use App\Domain\Audit\Entity\LogementCollection;

final class XMLAuditDeserializer
{
    public function __construct(
        private readonly XMLEnveloppeDeserializer $enveloppe_deserializer,
        private readonly XMLVentilationDeserializer $ventilation_deserializer,
        private readonly XMLEcsDeserializer $ecs_deserializer,
        private readonly XMLChauffageDeserializer $chauffage_deserializer,
        private readonly XMLRefroidissementDeserializer $refroidissement_deserializer,
        private readonly XMLEclairageDeserializer $eclairage_deserializer,
        private readonly XMLProductionDeserializer $production_deserializer,
    ) {}

    public function deserialize(XMLElement $xml): Audit
    {
        $reader = XMLAuditReader::from($xml);

        $audit = new Audit(
            id: $reader->id(),
            date_etablissement: $reader->date_etablissement(),
            adresse: $reader->adresse(),
            batiment: $reader->batiment(),
            logements: new LogementCollection,
            enveloppe: $this->enveloppe_deserializer->deserialize($xml),
            ventilation: $this->ventilation_deserializer->deserialize($xml),
            chauffage: $this->chauffage_deserializer->deserialize($xml),
            ecs: $this->ecs_deserializer->deserialize($xml),
            refroidissement: $this->refroidissement_deserializer->deserialize($xml),
            eclairage: $this->eclairage_deserializer->deserialize($xml),
            production: $this->production_deserializer->deserialize($xml),
            data: AuditData::create(),
        );

        return $audit;
    }
}
