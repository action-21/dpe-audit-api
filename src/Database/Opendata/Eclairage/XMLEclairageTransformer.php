<?php

namespace App\Database\Opendata\Eclairage;

use App\Database\Opendata\Audit\XMLAuditTransformer;
use App\Database\Opendata\XMLElement;
use App\Domain\Eclairage\Eclairage;

final class XMLEclairageTransformer
{
    public function __construct(
        private XMLAuditTransformer $audit_transformer,
    ) {}

    public function transform(XMLElement $root): Eclairage
    {
        $audit = $this->audit_transformer->transform($root);
        return Eclairage::create(audit: $audit);
    }
}
