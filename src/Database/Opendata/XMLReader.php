<?php

namespace App\Database\Opendata;

abstract class XMLReader
{
    public function __construct(private XMLElement $xml) {}

    public static function from(XMLElement $xml): static
    {
        return new static($xml);
    }

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
