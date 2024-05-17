<?php

namespace App\Database\Opendata\Logement;

use App\Database\Opendata\{XMLElement, XMLReaderIterator};
use App\Domain\Common\Identifier\Uuid;

final class XMLLogementReader extends XMLReaderIterator
{
    public function __construct(private XMLNiveauReader $niveau_reader)
    {
    }

    public function id(): \Stringable
    {
        return Uuid::create();
    }

    // Données déduites

    public function description(): string
    {
        return 'Logement non décrit';
    }

    /**
     * TODO: implémenter la méthode immeuble
     */
    public function surface_habitable(): float
    {
        return (float) $this->get()->findOneOrError('.//surface_habitable_logement');
    }

    /**
     * TODO: implémenter la méthode immeuble
     */
    public function hsp(): float
    {
        return (float) $this->get()->findOneOrError('.//hsp');
    }

    public function niveau_reader(): XMLNiveauReader
    {
        return $this->niveau_reader->read($this->get(), $this);
    }

    public function read(XMLElement $xml): self
    {
        $this->array = [$xml->findOneOfOrError(['/audit/logement_collection//logement[.//enum_scenario_id="0"]', '/dpe/logement'])];
        return $this;
    }
}
