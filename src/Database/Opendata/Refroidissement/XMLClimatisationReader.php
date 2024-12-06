<?php

namespace App\Database\Opendata\Refroidissement;

use App\Database\Opendata\XMLReader;
use App\Domain\Common\Type\Id;
use App\Domain\Refroidissement\Enum\{EnergieGenerateur, TypeGenerateur};

final class XMLClimatisationReader extends XMLReader
{
    public function id(): Id
    {
        return Id::from($this->reference());
    }

    public function reference(): string
    {
        return $this->xml()->findOneOrError('.//reference')->id();
    }

    public function description(): string
    {
        return $this->xml()->findOne('.//description')?->strval() ?? 'Installation de refroidissement non décrite';
    }

    public function type_generateur(): TypeGenerateur
    {
        return TypeGenerateur::from_enum_type_generateur_fr_id($this->enum_type_generateur_fr_id());
    }

    public function energie_generateur(): EnergieGenerateur
    {
        return ($value = $this->enum_type_energie_id())
            ? EnergieGenerateur::from_enum_type_energie_id($value)
            : EnergieGenerateur::from_enum_type_generateur_fr_id($this->enum_type_generateur_fr_id());
    }

    public function surface(): float
    {
        return $this->xml()->findOneOrError('.//surface_clim')->floatval();
    }

    public function annee_installation(): int
    {
        return match ($this->enum_type_generateur_fr_id()) {
            1, 4, 8, 12, 16 => 2007,
            2, 5, 9, 13, 17 => 2014,
            6, 10, 14, 18 => 2016,
            3, 7, 11, 15, 19 => $this->xml()->annee_etablissement(),
            20, 21, 22, 23 => match ($this->enum_periode_installation_fr_id()) {
                1 => 2007,
                2 => 2014,
                3 => 2016,
            }
        };
    }

    public function enum_type_generateur_fr_id(): int
    {
        return $this->xml()->findOneOrError('.//enum_type_generateur_fr_id')->intval();
    }

    public function enum_periode_installation_fr_id(): int
    {
        return $this->xml()->findOneOrError('.//enum_periode_installation_fr_id')->intval();
    }

    public function enum_type_energie_id(): ?int
    {
        return $this->xml()->findOne('.//enum_type_energie_id')?->intval();
    }

    public function eer(): float
    {
        return $this->xml()->findOneOrError('.//eer')->intval();
    }
}
