<?php

namespace App\Database\Opendata;

abstract class XMLDeserializer
{
    abstract public function deserialize(XMLElement $xml): object;
}
