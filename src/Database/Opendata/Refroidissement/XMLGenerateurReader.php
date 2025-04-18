<?php

namespace App\Database\Opendata\Refroidissement;

use App\Database\Opendata\XMLReader;
use App\Domain\Common\ValueObject\{Annee, Id};
use App\Domain\Refroidissement\Enum\{EnergieGenerateur, TypeGenerateur};

final class XMLGenerateurReader extends XMLReader
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
        return $this->findOne('.//description')?->strval() ?? 'Générateur de refroidissement non décrit';
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

    public function annee_installation(): Annee
    {
        return match ($this->enum_type_generateur_fr_id()) {
            1, 4, 8, 12, 16 => Annee::from(2007),
            2, 5, 9, 13, 17 => Annee::from(2014),
            6, 10, 14, 18 => Annee::from(2016),
            3, 7, 11, 15, 19 => $this->audit()->annee_etablissement(),
            20, 21, 22, 23 => match ($this->enum_periode_installation_fr_id()) {
                1 => Annee::from(2007),
                2 => Annee::from(2014),
                3 => $this->audit()->annee_etablissement(),
            }
        };
    }

    public function enum_type_generateur_fr_id(): int
    {
        return $this->findOneOrError('.//enum_type_generateur_fr_id')->intval();
    }

    public function enum_periode_installation_fr_id(): int
    {
        return $this->findOneOrError('.//enum_periode_installation_fr_id')->intval();
    }

    public function enum_type_energie_id(): ?int
    {
        return $this->findOne('.//enum_type_energie_id')?->intval();
    }

    public function eer(): float
    {
        return $this->findOneOrError('.//eer')->intval();
    }
}
