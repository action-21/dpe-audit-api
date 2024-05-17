<?php

namespace App\Database\Opendata\Ventilation;

use App\Database\Opendata\{XMLElement, XMLReaderIterator};
use App\Domain\Ventilation\Enum\{TypeInstallation, TypeVentilation};
use App\Domain\Ventilation\ValueObject\{AnneeInstallation, Surface};

final class XMLVentilationReader extends XMLReaderIterator
{
    private XMLInstallationVentilationReader $context;

    public function id(): \Stringable
    {
        return Reference::create($this->reference());
    }

    public function reference(): string
    {
        return $this->get()->findOneOrError('.//reference');
    }

    public function description(): string
    {
        return ($value = $this->get()->findOne('.//description')) ? (string) $value : 'Ventilation non décrite';
    }

    public function surface_ventilee(): Surface
    {
        return Surface::from((float) $this->get()->findOneOrError('.//surface_ventile'));
    }

    public function enum_type_ventilation_id(): int
    {
        return (int) $this->get()->findOneOrError('.//enum_type_ventilation_id');
    }

    // Données déduites

    public function type_installation(): ?TypeInstallation
    {
        return TypeInstallation::try_from_enum_type_ventilation_id($this->enum_type_ventilation_id());
    }

    public function type_ventilation(): TypeVentilation
    {
        return TypeVentilation::from_enum_type_ventilation_id($this->enum_type_ventilation_id());
    }

    public function annee_installation(): ?AnneeInstallation
    {
        return AnneeInstallation::try_from_enum_type_installation_id($this->enum_type_ventilation_id());
    }

    public function read(XMLElement $xml, XMLInstallationVentilationReader $context): self
    {
        $this->context = $context;
        $this->array = $xml->findManyOrError('.//ventilation_collection//ventilation');
        return $this;
    }
}
