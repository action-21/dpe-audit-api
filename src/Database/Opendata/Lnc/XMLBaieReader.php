<?php

namespace App\Database\Opendata\Lnc;

use App\Database\Opendata\XMLReaderIterator;
use App\Domain\Common\Type\Id;
use App\Domain\Lnc\Enum\{Mitoyennete};

final class XMLBaieReader extends XMLReaderIterator
{
    public function id(): Id
    {
        return $this->xml()->findOneOrError('.//reference')->id();
    }

    public function description(): string
    {
        return $this->xml()->findOne('.//description')?->strval() ?? "Baie non décrite";
    }

    public function orientation(): ?float
    {
        return $this->xml()->findOneOrError('.//enum_orientation_id')->orientation();
    }

    public function inclinaison(): ?float
    {
        return $this->xml()->findOneOrError('.//enum_inclinaison_vitrage_id')->inclinaison();
    }

    public function mitoyennete(): Mitoyennete
    {
        return Mitoyennete::EXTERIEUR;
    }

    public function enum_inclinaison_vitrage_id(): int
    {
        return $this->xml()->findOneOrError('.//enum_inclinaison_vitrage_id')->intval();
    }

    public function surface_totale(): float
    {
        return $this->xml()->findOneOrError('.//surface_totale_baie')->floatval();
    }
}
