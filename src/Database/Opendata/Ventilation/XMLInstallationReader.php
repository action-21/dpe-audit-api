<?php

namespace App\Database\Opendata\Ventilation;

use App\Database\Opendata\XMLReader;
use App\Domain\Common\ValueObject\Id;

final class XMLInstallationReader extends XMLReader
{
    /** @return XMLSystemeReader[] */
    public function read_systemes(): array
    {
        return [XMLSystemeReader::from($this->xml())];
    }

    public function id(): Id
    {
        return $this->findOneOrError('.//reference')->id();
    }

    public function description(): string
    {
        return $this->findOne('.//description')?->strval() ?? 'Installation non décrite';
    }

    public function surface(): float
    {
        return $this->findOneOrError('.//surface_ventile')->floatval();
    }
}
