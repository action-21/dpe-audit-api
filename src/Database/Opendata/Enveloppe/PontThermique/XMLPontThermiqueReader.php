<?php

namespace App\Database\Opendata\Enveloppe\PontThermique;

use App\Database\Opendata\XMLReader;
use App\Domain\Common\ValueObject\Id;
use App\Domain\Enveloppe\Enum\PontThermique\TypeLiaison;

final class XMLPontThermiqueReader extends XMLReader
{
    public function id(): Id
    {
        return $this->findOneOrError('.//reference')->id();
    }

    public function reference(): string
    {
        return $this->findOneOrError('.//reference')->reference();
    }

    public function reference_1(): ?string
    {
        return $this->findOne('.//reference_1')?->reference();
    }

    public function reference_2(): ?string
    {
        return $this->findOne('.//reference_2')?->reference();
    }

    public function references(): array
    {
        return array_filter([
            $this->reference(),
            $this->reference_1(),
            $this->reference_2(),
        ]);
    }

    public function mur_id(): Id
    {
        foreach ($this->enveloppe()->murs() as $item) {
            if ($item->match($this->references())) {
                return $item->id();
            }
        }
        throw new \DomainException("Mur non trouvé pour le pont thermique {$this->reference()}", 400);
    }

    public function paroi_id(): ?Id
    {
        if (in_array($this->type_liaison(), [TypeLiaison::REFEND_MUR, TypeLiaison::PLANCHER_INTERMEDIAIRE_MUR])) {
            return null;
        }
        if (\in_array($this->type_liaison(), [TypeLiaison::MENUISERIE_MUR])) {
            foreach ($this->enveloppe()->baies() as $item) {
                if ($item->match($this->references())) {
                    return $item->id();
                }
            }
            foreach ($this->enveloppe()->portes() as $item) {
                if ($item->match($this->references())) {
                    return $item->id();
                }
            }
            throw new \DomainException("Menuiserie non trouvée pour le pont thermique {$this->reference()}");
        }
        foreach ($this->enveloppe()->planchers_bas() as $item) {
            if ($item->match($this->references())) {
                return $item->id();
            }
        }
        foreach ($this->enveloppe()->planchers_hauts() as $item) {
            if ($item->match($this->references())) {
                return $item->id();
            }
        }
        throw new \DomainException("Plancher non trouvé pour le pont thermique {$this->reference()}");
    }

    public function description(): string
    {
        return $this->findOne('.//description')?->strval() ?? 'Pont thermique non décrit';
    }

    public function type_liaison(): TypeLiaison
    {
        return TypeLiaison::from_enum_type_liaison_id($this->enum_type_liaison_id());
    }

    public function longueur(): float
    {
        return $this->findOneOrError('.//l')->floatval();
    }

    public function pont_thermique_partiel(): bool
    {
        return $this->findOneOrError('.//pourcentage_valeur_pont_thermique')->floatval() === 1 ? false : true;
    }

    public function k_saisi(): ?float
    {
        return $this->findOne('.//k_saisi')?->floatval();
    }

    public function enum_type_liaison_id(): string
    {
        return $this->findOne('.//enum_type_liaison_id')->strval();
    }

    // Données intermédaires

    public function k(): float
    {
        return $this->findOneOrError('.//k')->floatval();
    }
}
