<?php

namespace App\Database\Opendata;

use App\Database\Opendata\Audit\XMLAuditReader;
use App\Database\Opendata\Batiment\XMLBatimentReader;
use App\Database\Opendata\Enveloppe\XMLEnveloppeReader;

final class XMLOpendataReader
{
    private XMLElement $xml;

    public function __construct(
        private XMLAuditReader $audit_reader,
        private XMLBatimentReader $batiment_reader,
        private XMLEnveloppeReader $enveloppe_reader,
        
    ) {
    }

    public function audit_reader(): XMLAuditReader
    {
        return ($this->audit_reader)($this->xml);
    }

    public function batiment_reader(): XMLBatimentReader
    {
        return ($this->batiment_reader)($this->xml);
    }

    public function enveloppe_reader(): XMLEnveloppeReader
    {
        return ($this->enveloppe_reader)($this->xml);
    }

    public function __invoke(XMLElement $xml): self
    {
        $this->xml = $xml;
        return $this;
    }
}
