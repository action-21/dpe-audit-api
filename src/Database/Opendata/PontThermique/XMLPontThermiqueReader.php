<?php

namespace App\Database\Opendata\PontThermique;

use App\Database\Opendata\XMLReader;
use App\Domain\Common\Type\Id;
use App\Domain\PontThermique\Enum\TypeLiaison;

final class XMLPontThermiqueReader extends XMLReader
{
    public function id(): Id
    {
        return $this->xml()->findOneOrError('.//reference')->id();
    }

    public function reference(): string
    {
        return $this->xml()->findOneOrError('.//reference')->reference();
    }

    public function reference_1(): ?string
    {
        return $this->xml()->findOne('.//reference_1')?->reference();
    }

    public function reference_2(): ?string
    {
        return $this->xml()->findOne('.//reference_2')?->reference();
    }

    public function mur_id(): Id
    {
        foreach ($this->xml()->read_enveloppe()->read_murs() as $item) {
            if ($this->reference_1() && $item->match($this->reference_1())) {
                return $item->id();
            }
            if ($this->reference_2() && $item->match($this->reference_2())) {
                return $item->id();
            }
        }
        throw new \DomainException("Mur non trouvé pour le pont thermique {$this->reference()}", 400);
    }

    public function plancher_id(): ?Id
    {
        if (!\in_array($this->type_liaison(), [TypeLiaison::PLANCHER_BAS_MUR, TypeLiaison::PLANCHER_HAUT_MUR])) {
            return null;
        }
        foreach ($this->xml()->read_enveloppe()->read_planchers_bas() as $item) {
            if ($this->reference_1() && $item->match($this->reference_1())) {
                return $item->id();
            }
            if ($this->reference_2() && $item->match($this->reference_2())) {
                return $item->id();
            }
        }
        foreach ($this->xml()->read_enveloppe()->read_planchers_hauts() as $item) {
            if ($this->reference_1() && $item->match($this->reference_1())) {
                return $item->id();
            }
            if ($this->reference_2() && $item->match($this->reference_2())) {
                return $item->id();
            }
        }
        throw new \DomainException("Plancher non trouvé pour le pont thermique {$this->reference()}", 400);
    }

    public function ouverture_id(): ?Id
    {
        if (!\in_array($this->type_liaison(), [TypeLiaison::MENUISERIE_MUR])) {
            return null;
        }
        foreach ($this->xml()->read_enveloppe()->read_baies() as $item) {
            if ($this->reference_1() && $item->match($this->reference_1())) {
                return $item->id();
            }
            if ($this->reference_2() && $item->match($this->reference_2())) {
                return $item->id();
            }
        }
        foreach ($this->xml()->read_enveloppe()->read_portes() as $item) {
            if ($this->reference_1() && $item->match($this->reference_1())) {
                return $item->id();
            }
            if ($this->reference_2() && $item->match($this->reference_2())) {
                return $item->id();
            }
        }
        throw new \DomainException("Menuiserie non trouvée pour le pont thermique {$this->reference()}", 400);
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
