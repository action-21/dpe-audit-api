<?php

namespace App\Database\Opendata;

abstract class XMLReader
{
    public function __construct(protected XMLElement $xml) {}

    public static function from(XMLElement $xml): static
    {
        return new static($xml);
    }

    public function xml(): XMLElement
    {
        return $this->xml;
    }
}
