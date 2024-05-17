<?php

namespace App\Database\Opendata\Audit;

use App\Database\Opendata\Batiment\XMLBatimentParser;
use App\Database\Opendata\XMLElement;
use App\Domain\Audit\Audit;

final class XMLAuditParser
{
    public function __construct(
        private XMLAuditReader $audit_reader,
        private XMLBatimentParser $batiment_parser,
    ) {
    }

    /**
     * TODO: DPE appartement depuis données DPE immeuble
     */
    public function parse(XMLElement $xml): Audit
    {
        $reader = $this->audit_reader->read($xml);

        $entity = new Audit(
            id: $reader->id(),
            date_creation: new \DateTimeImmutable,
            methode_calcul: $reader->methode_calcul(),
            perimetre_application: $reader->perimetre_application(),
            auditeur: $reader->auditeur(),
            batiment: null,
        );

        $this->batiment_parser->parse($xml, $entity);

        return $entity;
    }
}
