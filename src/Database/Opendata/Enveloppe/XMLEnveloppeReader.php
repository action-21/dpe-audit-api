<?php

namespace App\Database\Opendata\Enveloppe;

use App\Database\Opendata\XMLReader;
use App\Domain\Enveloppe\Enum\Exposition;

final class XMLEnveloppeReader extends XMLReader
{
    public function plusieurs_facade_exposee(): bool
    {
        return $this->xml()->findOneOrError('//plusieurs_facade_exposee')->getValue();
    }

    public function q4pa_conv(): ?float
    {
        return $this->xml()->findOne('//q4pa_conv_saisi')?->floatval();
    }

    public function exposition(): Exposition
    {
        return $this->plusieurs_facade_exposee() ? Exposition::EXPOSITION_MULTIPLE : Exposition::EXPOSITION_SIMPLE;
    }
}
