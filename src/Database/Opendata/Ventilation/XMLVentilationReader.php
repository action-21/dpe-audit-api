<?php

namespace App\Database\Opendata\Ventilation;

use App\Database\Opendata\XMLReaderIterator;
use App\Domain\Common\Type\Id;
use App\Domain\Ventilation\Enum\{ModeExtraction, ModeInsufflation, TypeGenerateur, TypeSysteme, TypeVentilation};

final class XMLVentilationReader extends XMLReaderIterator
{
    public function id(): Id
    {
        return Id::from($this->reference());
    }

    public function reference(): string
    {
        return $this->xml()->findOneOrError('.//reference')->strval();
    }

    public function description(): string
    {
        return $this->xml()->findOne('.//description')?->strval() ?? 'Ventilation non décrite';
    }

    public function type_ventilation(): TypeVentilation
    {
        return TypeVentilation::from_enum_type_ventilation_id($this->enum_type_ventilation_id());
    }

    public function type_systeme(): TypeSysteme
    {
        return TypeSysteme::from_enum_type_ventilation_id($this->enum_type_ventilation_id());
    }

    public function type_generateur(): ?TypeGenerateur
    {
        return TypeGenerateur::from_enum_type_ventilation_id($this->enum_type_ventilation_id());
    }

    public function surface(): float
    {
        return $this->xml()->findOneOrError('.//surface_ventile')->floatval();
    }

    public function annee_installation(): ?int
    {
        return match ($this->enum_type_ventilation_id()) {
            3 => 1981,
            4, 7, 10, 13, 26, 29 => 2000,
            5, 8, 11, 14, 19, 21, 23, 27, 30, 32, 35, 37 => 2012,
            6, 9, 12, 15, 20, 22, 24, 28, 31, 33, 36, 38 => $this->xml()->annee_etablissement(),
            default => null,
        };
    }

    public function presence_echangeur_thermique(): bool
    {
        return match ($this->enum_type_ventilation_id()) {
            19, 20, 21, 22, 37, 38 => true,
            23, 24, 35, 36 => false,
            default => false,
        };
    }

    public function generateur_collectif(): bool
    {
        return match ($this->enum_type_ventilation_id()) {
            21, 22 => true,
            default => false,
        };
    }

    public function mode_extraction(): ?ModeExtraction
    {
        return ModeExtraction::from_enum_type_ventilation_id($this->enum_type_ventilation_id());
    }

    public function mode_insufflation(): ?ModeInsufflation
    {
        return ModeInsufflation::from_enum_type_ventilation_id($this->enum_type_ventilation_id());
    }

    public function enum_type_ventilation_id(): int
    {
        return $this->xml()->findOneOrError('.//enum_type_ventilation_id')->intval();
    }
}
