<?php

namespace App\Database\Opendata\Refroidissement;

use App\Database\Opendata\XMLReader;
use App\Domain\Common\ValueObject\Id;

final class XMLSystemeReader extends XMLReader
{
    public function id(): Id
    {
        return Id::from($this->reference());
    }

    public function installation_id(): Id
    {
        return $this->id();
    }

    public function generateur_id(): Id
    {
        return $this->id();
    }

    public function reference(): string
    {
        return $this->findOneOrError('.//reference')->id();
    }
}
