<?php

namespace App\Serializer\Opendata;

use App\Database\Opendata\Audit\XMLAuditReader;
use App\Database\Opendata\XMLElement;
use App\Domain\Audit\Audit;
use App\Domain\Audit\Entity\Logement;

final class XMLAuditDeserializer
{
    private XMLAuditReader $reader;
    private Audit $entity;

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
        $reader = $this->reader = XMLAuditReader::from($xml);

        $this->entity = Audit::create(
            id: $reader->id(),
            date_etablissement: $reader->date_etablissement(),
            adresse: $reader->adresse(),
            batiment: $reader->batiment(),
            enveloppe: $this->enveloppe_deserializer->deserialize($xml),
            ventilation: $this->ventilation_deserializer->deserialize($xml),
            chauffage: $this->chauffage_deserializer->deserialize($xml),
            ecs: $this->ecs_deserializer->deserialize($xml),
            refroidissement: $this->refroidissement_deserializer->deserialize($xml),
            production: $this->production_deserializer->deserialize($xml),
        );

        $this->deserialize_logements();

        return $this->entity;
    }

    private function deserialize_logements(): void
    {
        foreach ($this->reader->logements() as $reader) {
            $this->entity->add_logement(Logement::create(
                id: $reader->id(),
                audit: $this->entity,
                description: $reader->description(),
                position: $reader->position(),
                typologie: $reader->typologie(),
                surface_habitable: $reader->surface_habitable(),
                hauteur_sous_plafond: $reader->hauteur_sous_plafond(),
            ));
        }
    }
}
