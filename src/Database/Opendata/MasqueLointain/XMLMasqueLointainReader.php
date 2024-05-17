<?php

namespace App\Database\Opendata\MasqueLointain;

use App\Database\Opendata\{XMLElement, XMLReaderIterator};
use App\Domain\Common\Identifier\Reference;

final class XMLMasqueLointainReader extends XMLReaderIterator
{
    public function __construct(
        private XMLMasqueLointainHomogeneReader $masque_lointain_homogene_reader,
        private XMLMasqueLointainNonHomogeneReader $masque_lointain_non_homogene_reader,
    ) {
    }

    public function reference_baie(): \Stringable
    {
        return Reference::create($this->get()->findOneOrError('.//reference')->getValue());
    }

    public function tv_coef_masque_lointain_homogene_id(): ?int
    {
        return ($value = $this->get()->findOne('.//tv_coef_masque_lointain_homogene_id')?->getValue()) ? (int) $value : null;
    }

    public function masque_lointain_homogene_reader(): XMLMasqueLointainHomogeneReader
    {
        return $this->masque_lointain_homogene_reader->read($this);
    }

    public function masque_lointain_non_homogene_reader(): XMLMasqueLointainNonHomogeneReader
    {
        return $this->masque_lointain_non_homogene_reader->read($this);
    }

    public function read(XMLElement $xml): self
    {
        $xml = $xml->findOneOfOrError(['/audit/logement_collection//logement[.//enum_scenario_id="0"]', '/dpe/logement']);
        $this->array = $xml->findMany('.//baie_vitree_collection//baie_vitree');
        return $this;
    }
}
