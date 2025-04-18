<?php

namespace App\Database\Opendata\Refroidissement;

use App\Database\Opendata\XMLReader;
use App\Domain\Common\ValueObject\Id;

final class XMLInstallationReader extends XMLReader
{
    public function id(): Id
    {
        return Id::from($this->reference());
    }

    public function reference(): string
    {
        return $this->findOneOrError('.//reference')->id();
    }

    public function description(): string
    {
        return $this->findOne('.//description')?->strval() ?? 'Installation de refroidissement non décrite';
    }

    public function surface(): float
    {
        return $this->findOneOrError('.//surface_clim')->floatval();
    }
}
