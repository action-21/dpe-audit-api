<?php

namespace App\Database\Opendata\Ventilation;

use App\Database\Opendata\XMLElement;
use App\Domain\Common\Identifier\Uuid;

final class XMLInstallationVentilationReader
{
    private XMLElement $xml;

    public function __construct(private XMLVentilationReader $ventilation_reader)
    {
    }

    public function id(): \Stringable
    {
        return Uuid::create();
    }

    public function ventilation_reader(): XMLVentilationReader
    {
        return $this->ventilation_reader->read($this->xml, $this);
    }

    /**
     * TODO: identifier les installations par appartement dans le cas d'un Audit-DPE immeuble
     */
    public function read(XMLElement $xml): self
    {
        $this->xml = $xml;
        return $this;
    }
}
