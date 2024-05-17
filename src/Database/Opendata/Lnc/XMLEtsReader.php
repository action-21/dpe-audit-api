<?php

namespace App\Database\Opendata\Lnc;

use App\Database\Opendata\{XMLElement, XMLReaderIterator};
use App\Domain\Common\Identifier\Reference;
use App\Domain\Lnc\Enum\{TypeLnc};

final class XMLEtsReader extends XMLReaderIterator
{
    public function __construct(private XMLEtsBaieReader $baie_reader)
    {
    }

    public function id(): \Stringable
    {
        return Reference::create($this->reference());
    }

    public function reference(): string
    {
        return $this->get()->findOneOrError('.//reference')->getValue();
    }

    public function description(): string
    {
        return $this->get()->findOne('.//description')?->getValue() ?? "Local non chauffé non décrit";
    }

    public function type_lnc(): TypeLnc
    {
        return TypeLnc::ESPACE_TAMPON_SOLARISE;
    }

    // Données intermédiaires

    public function tv_coef_transparence_ets_id(): int
    {
        return (int) $this->get()->findOne('.//tv_coef_transparence_ets_id')->getValue();
    }

    public function coef_transparence_ets(): float
    {
        return (float) $this->get()->findOne('.//coef_transparence_ets')->getValue();
    }

    public function bver(): float
    {
        return (float) $this->get()->findOne('.//bver')->getValue();
    }

    public function baie_reader(): XMLEtsBaieReader
    {
        return $this->baie_reader->read($this->get(), $this);
    }

    public function read(XMLElement $xml): self
    {
        $this->array = $xml->findMany('//ets_collection/ets');
        return $this;
    }
}
