<?php

namespace App\Database\Opendata\Ventilation;

use App\Database\Opendata\XMLReader;
use App\Domain\Common\ValueObject\{Annee, Id};
use App\Domain\Ventilation\Enum\{TypeGenerateur, TypeVentilation, TypeVmc};

final class XMLGenerateurReader extends XMLReader
{
    public function supports(): bool
    {
        return $this->type_ventilation() === TypeVentilation::VENTILATION_MECANIQUE;
    }

    public function id(): Id
    {
        return $this->findOneOrError('.//reference')->id();
    }

    public function description(): string
    {
        return $this->findOne('.//description')?->strval() ?? 'Ventilation non décrite';
    }

    public function type_ventilation(): TypeVentilation
    {
        return TypeVentilation::from_enum_type_ventilation_id($this->enum_type_ventilation_id());
    }

    public function type_generateur(): ?TypeGenerateur
    {
        return TypeGenerateur::from_enum_type_ventilation_id($this->enum_type_ventilation_id());
    }

    public function type_vmc(): ?TypeVmc
    {
        if ($this->type_generateur()?->is_vmc()) {
            return TypeVmc::from_enum_type_ventilation_id($this->enum_type_ventilation_id())
                ?? TypeVmc::from_pvent_moy($this->pvent_moy() ?? 0)
                ?? TypeVmc::default();
        }
        return null;
    }

    public function annee_installation(): ?Annee
    {
        return match ($this->enum_type_ventilation_id()) {
            3 => Annee::from(1981),
            4, 7, 10, 13, 26, 29 => Annee::from(2000),
            5, 8, 11, 14, 19, 21, 23, 27, 30, 32, 35, 37 => Annee::from(2012),
            6, 9, 12, 15, 20, 22, 24, 28, 31, 33, 36, 38 => $this->audit()->annee_etablissement(),
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

    public function enum_type_ventilation_id(): int
    {
        return $this->findOneOrError('.//enum_type_ventilation_id')->intval();
    }

    public function pvent_moy(): ?float
    {
        return $this->findOne('.//pvent_moy')?->floatval();
    }
}
