<?php

namespace App\Database\Opendata\Climatisation;

use App\Database\Opendata\{XMLElement, XMLReaderIterator};
use App\Domain\Common\Identifier\Reference;
use App\Domain\Climatisation\Enum\{EnergieGenerateur, TypeGenerateur};
use App\Domain\Climatisation\ValueObject\{AnneeInstallation, Seer, Surface};

final class XMLGenerateurReader extends XMLReaderIterator
{
    private XMLInstallationClimatisationReader $context;

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
        return ($value = $this->get()->findOne('.//description')) ? (string) $value : 'Climatisation non décrite';
    }

    public function surface_clim(): Surface
    {
        return Surface::from((float) $this->get()->findOneOrError('.//surface_clim'));
    }

    public function enum_periode_installation_fr_id(): int
    {
        return (int) $this->get()->findOneOrError('.//enum_periode_installation_fr_id');
    }

    public function enum_type_generateur_fr_id(): int
    {
        return (int) $this->get()->findOneOrError('.//enum_type_generateur_fr_id');
    }

    public function enum_type_energie_id(): ?int
    {
        return ($value = $this->get()->findOne('.//enum_type_energie_id')) ? (int) $value : null;
    }

    // Données déduites

    public function type_generateur(): TypeGenerateur
    {
        return TypeGenerateur::from_enum_type_generateur_fr_id($this->enum_type_generateur_fr_id());
    }

    public function annee_installation(): ?AnneeInstallation
    {
        return AnneeInstallation::from_enum_periode_installation_fr_id($this->enum_periode_installation_fr_id());
    }

    public function energie(): ?EnergieGenerateur
    {
        return $this->enum_type_energie_id() ? EnergieGenerateur::from_enum_type_energie_id($this->enum_type_energie_id()) : null;
    }

    public function seer_saisi(): ?Seer
    {
        return null;
    }

    public function read(XMLElement $xml, XMLInstallationClimatisationReader $context): self
    {
        $this->context = $context;
        $this->array = $xml->findManyOrError('.//ventilation_collection//ventilation');
        return $this;
    }
}
