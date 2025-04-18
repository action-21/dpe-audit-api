<?php

namespace App\Database\Opendata\Ventilation;

use App\Database\Opendata\XMLReader;
use App\Domain\Common\ValueObject\Id;
use App\Domain\Ventilation\Enum\TypeVentilation;

final class XMLSystemeReader extends XMLReader
{
    public function id(): Id
    {
        return $this->findOneOrError('.//reference')->id();
    }

    public function generateur_id(): ?Id
    {
        return $this->type_ventilation() === TypeVentilation::VENTILATION_MECANIQUE ? $this->id() : null;
    }

    public function installation_id(): Id
    {
        return $this->id();
    }

    public function description(): string
    {
        return $this->findOne('.//description')?->strval() ?? 'Système non décrit';
    }

    public function type_ventilation(): TypeVentilation
    {
        return TypeVentilation::from_enum_type_ventilation_id($this->enum_type_ventilation_id());
    }

    public function enum_type_ventilation_id(): int
    {
        return $this->findOneOrError('.//enum_type_ventilation_id')->intval();
    }
}
