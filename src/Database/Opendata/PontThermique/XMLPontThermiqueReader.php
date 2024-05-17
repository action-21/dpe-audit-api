<?php

namespace App\Database\Opendata\PontThermique;

use App\Database\Opendata\{XMLElement, XMLReaderIterator};
use App\Domain\Common\Identifier\Reference;
use App\Domain\PontThermique\Enum\TypeLiaison;
use App\Domain\PontThermique\ValueObject\{Kpt, Longueur};

final class XMLPontThermiqueReader extends XMLReaderIterator
{
    public function id(): \Stringable
    {
        return Reference::create($this->reference());
    }

    public function reference(): string
    {
        return $this->get()->findOneOrError('.//reference')->getValue();
    }

    public function reference_1(): ?\Stringable
    {
        return ($value = $this->get()->findOne('.//reference_1')?->getValue()) ? Reference::create($value) : null;
    }

    public function reference_2(): ?\Stringable
    {
        return ($value = $this->get()->findOne('.//reference_2')?->getValue()) ? Reference::create($value) : null;
    }

    public function description(): string
    {
        return $this->get()->findOne('.//description')?->getValue() ?? "Pont thermique non décrit";
    }

    public function tv_pont_thermique_id(): ?int
    {
        return ($value = $this->get()->findOne('.//tv_pont_thermique_id')?->getValue()) ? (int) $value : null;
    }

    public function pourcentage_valeur_pont_thermique(): float
    {
        return (float) $this->get()->findOneOrError('.//pourcentage_valeur_pont_thermique')->getValue();
    }

    public function l(): Longueur
    {
        return Longueur::from((float) $this->get()->findOneOrError('.//l')->getValue());
    }

    public function enum_type_liaison_id(): int
    {
        return (int) $this->get()->findOneOrError('.//enum_type_liaison_id')->getValue();
    }

    public function enum_type_liaision(): TypeLiaison
    {
        return TypeLiaison::from($this->enum_type_liaison_id());
    }

    public function k_saisi(): ?Kpt
    {
        return ($value = $this->get()->findOne('.//k_saisi')?->getValue()) ? Kpt::from((float) $value) : null;
    }

    public function k(): ?Kpt
    {
        return ($value = $this->get()->findOne('.//k')?->getValue()) ? Kpt::from((float) $value) : null;
    }

    public function enum_etat_composant_id(): ?int
    {
        return ($value = $this->get()->findOne('.//enum_etat_composant_id')?->getValue()) ? (int) $value : null;
    }

    // Données déduites

    public function pont_thermique_partiel(): bool
    {
        return (int) $this->pourcentage_valeur_pont_thermique() === 1 ? false : true;
    }

    public function read(XMLElement $xml): self
    {
        $xml = $xml->findOneOfOrError(['/audit/logement_collection//logement[.//enum_scenario_id="0"]', '/dpe/logement']);
        $this->array = $xml->findMany('.//pont_thermique_collection//pont_thermique');
        return $this;
    }
}
