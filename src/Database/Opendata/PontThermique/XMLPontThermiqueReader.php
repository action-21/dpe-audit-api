<?php

namespace App\Database\Opendata\PontThermique;

use App\Database\Opendata\XMLReaderIterator;
use App\Domain\Common\Type\Id;
use App\Domain\PontThermique\Enum\TypeLiaison;

final class XMLPontThermiqueReader extends XMLReaderIterator
{
    public function id(): Id
    {
        return Id::from($this->reference());
    }

    public function id_paroi_1(): ?Id
    {
        return ($value = $this->reference_1()) ? Id::from($value) : null;
    }

    public function id_paroi_2(): ?Id
    {
        return ($value = $this->reference_2()) ? Id::from($value) : null;
    }

    public function description(): string
    {
        return $this->xml()->findOne('.//description')?->strval() ?? 'Pont thermique non décrit';
    }

    public function type_liaison(): TypeLiaison
    {
        return TypeLiaison::from_enum_type_liaison_id($this->enum_type_liaison_id());
    }

    public function longueur(): float
    {
        return $this->xml()->findOneOrError('.//l')->floatval();
    }

    public function pont_thermique_partiel(): bool
    {
        return $this->xml()->findOneOrError('.//pourcentage_valeur_pont_thermique')->floatval() === 1 ? false : true;
    }

    public function k_saisi(): ?float
    {
        return $this->xml()->findOne('.//k_saisi')?->floatval();
    }

    public function reference(): string
    {
        return $this->xml()->findOneOrError('.//reference')->strval();
    }

    public function reference_1(): ?string
    {
        return $this->xml()->findOne('.//reference_1')?->strval();
    }

    public function reference_2(): ?string
    {
        return $this->xml()->findOne('.//reference_2')?->strval();
    }

    public function enum_type_liaison_id(): string
    {
        return $this->xml()->findOne('.//enum_type_liaison_id')->strval();
    }

    // Données intermédaires

    public function k(): float
    {
        return $this->xml()->findOneOrError('.//k')->floatval();
    }
}
