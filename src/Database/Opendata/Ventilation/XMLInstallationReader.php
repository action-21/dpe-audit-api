<?php

namespace App\Database\Opendata\Ventilation;

use App\Database\Opendata\XMLReader;
use App\Domain\Common\Type\Id;

/**
 * TODO: identifier les installations par appartement dans le cas d'un Audit-DPE immeuble
 */
final class XMLInstallationReader extends XMLReader
{
    /** @return XMLSystemeReader[] */
    public function read_systemes(): array
    {
        return [XMLSystemeReader::from($this->xml())];
    }

    public function id(): Id
    {
        return $this->xml()->findOneOrError('.//reference')->id();
    }

    public function description(): string
    {
        return $this->xml()->findOne('.//description')?->strval() ?? 'Ventilation non décrite';
    }

    public function surface(): float
    {
        return $this->xml()->findOneOrError('.//surface_ventile')->floatval();
    }
}
