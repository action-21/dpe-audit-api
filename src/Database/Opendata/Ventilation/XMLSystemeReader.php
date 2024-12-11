<?php

namespace App\Database\Opendata\Ventilation;

use App\Database\Opendata\XMLReader;
use App\Domain\Common\Type\Id;
use App\Domain\Ventilation\Enum\TypeVentilation;

final class XMLSystemeReader extends XMLReader
{
    public function id(): Id
    {
        return $this->xml()->findOneOrError('.//reference')->id();
    }

    public function generateur_id(): ?Id
    {
        return $this->type_ventilation() === TypeVentilation::VENTILATION_MECANIQUE ? $this->id() : null;
    }

    public function description(): string
    {
        return $this->xml()->findOne('.//description')?->strval() ?? 'Système non décrit';
    }

    public function type_ventilation(): TypeVentilation
    {
        return TypeVentilation::from_enum_type_ventilation_id($this->enum_type_ventilation_id());
    }

    public function enum_type_ventilation_id(): int
    {
        return $this->xml()->findOneOrError('.//enum_type_ventilation_id')->intval();
    }
}
