<?php

namespace App\Database\Opendata\Audit;

use App\Database\Opendata\XMLElement;
use App\Domain\Audit\Audit;

final class XMLAuditTransformer
{
    public function __construct(
        private XMLAuditReader $reader,
    ) {}

    public function transform(XMLElement $root): Audit
    {
        $reader = $this->reader->read($root);
        $audit = new Audit(
            id: $reader->id(),
            date_creation: $reader->date_etablissement(),
            adresse: $reader->adresse(),
            batiment: $reader->batiment(),
            logement: $reader->logement(),
        );
        return $audit;
    }
}
