<?php

namespace App\Database\Opendata\Enveloppe;

use App\Database\Opendata\XMLElement;
use App\Domain\Enveloppe\Enum\Exposition;
use App\Domain\Enveloppe\ValueObject\Permeabilite;
use App\Domain\Enveloppe\ValueObject\Q4PaConv;

final class XMLEnveloppeReader
{
    private XMLElement $xml;

    public function plusieurs_facade_exposee(): bool
    {
        return $this->xml->findOneOrError('//plusieurs_facade_exposee')->getValue();
    }

    public function q4pa_conv_saisi(): ?Q4PaConv
    {
        return ($value = $this->xml->findOne('//q4pa_conv_saisi')?->getValue()) ? Q4PaConv::from((float) $value) : null;
    }

    // Données déduites

    public function exposition(): Exposition
    {
        return Exposition::from_boolean($this->plusieurs_facade_exposee());
    }

    public function permeabilite(): Permeabilite
    {
        return new Permeabilite(
            exposition: $this->exposition(),
            q4pa_conv: $this->q4pa_conv_saisi(),
        );
    }

    public function read(XMLElement $xml): self
    {
        $this->xml = $xml;
        return $this;
    }
}
