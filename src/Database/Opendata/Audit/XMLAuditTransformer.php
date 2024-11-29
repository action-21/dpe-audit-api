<?php

namespace App\Database\Opendata\Audit;

use App\Database\Opendata\XMLElement;
use App\Domain\Audit\Audit;

final class XMLAuditTransformer
{
    public function transform(XMLElement $root): Audit
    {
        $reader = $root->read_audit();
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
