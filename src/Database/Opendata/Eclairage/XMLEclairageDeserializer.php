<?php

namespace App\Database\Opendata\Eclairage;

use App\Database\Opendata\XMLElement;
use App\Domain\Eclairage\Eclairage;

final class XMLEclairageDeserializer
{
    public function deserialize(XMLElement $xml): Eclairage
    {
        return Eclairage::create();
    }
}
