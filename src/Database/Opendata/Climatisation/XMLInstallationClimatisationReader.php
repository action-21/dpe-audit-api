<?php

namespace App\Database\Opendata\Climatisation;

use App\Database\Opendata\XMLElement;
use App\Domain\Common\Identifier\Uuid;

final class XMLInstallationClimatisationReader
{
    private XMLElement $xml;

    public function __construct(private XMLGenerateurReader $generateur_reader)
    {
    }

    public function id(): \Stringable
    {
        return Uuid::create();
    }

    public function generateur_reader(): XMLGenerateurReader
    {
        return $this->generateur_reader->read($this->xml, $this);
    }

    public static function apply(XMLElement $xml): bool
    {
        return \count($xml->findMany('.//climatisation_collection/climatisation')) > 0;
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
