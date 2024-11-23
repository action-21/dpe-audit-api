<?php

namespace App\Database\Opendata\Visite;

use App\Database\Opendata\XMLReaderIterator;
use App\Domain\Common\Type\Id;
use App\Domain\Visite\Enum\Typologie;

final class XMLLogementReader extends XMLReaderIterator
{
    public function id(): Id
    {
        return Id::create();
    }

    public function description(): float
    {
        return $this->xml()->findOneOrError('.//description')->strval();
    }

    public function surface_habitable(): float
    {
        return $this->xml()->findOneOrError('.//surface_habitable_logement')->floatval();
    }

    public function typologoie(): Typologie
    {
        return Typologie::from_enum_typologie_logement_id(
            id: $this->xml()->findOneOrError('.//enum_typologie_logement_id')->intval()
        );
    }
}
