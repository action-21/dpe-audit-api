<?php

namespace App\Database\Opendata;

abstract class XMLReader
{
    private XMLElement $xml;

    public function read(XMLElement $xml): static
    {
        $this->xml = $xml;
        return $this;
    }

    public function xml(): XMLElement
    {
        return $this->xml;
    }
}
