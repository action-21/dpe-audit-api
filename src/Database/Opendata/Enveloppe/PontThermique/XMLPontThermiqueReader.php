<?php

namespace App\Database\Opendata\Enveloppe\PontThermique;

use App\Database\Opendata\XMLReader;
use App\Domain\Common\ValueObject\Id;
use App\Domain\Enveloppe\Enum\PontThermique\TypeLiaison;

final class XMLPontThermiqueReader extends XMLReader
{
    public function supports(): bool
    {
        if (false === $this->mur_id()) {
            return false;
        }
        if (false === $this->paroi_id()) {
            return false;
        }
        return true;
    }

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

    /**
     * Si la référence au mur est rompue, on associe par défaut le pont thermique au premier mur trouvé
     * 
     * @return Id|false
     * 
     * @return false en cas d'erreur
     */
    public function mur_id(): Id|false
    {
        foreach ($this->enveloppe()->murs() as $item) {
            if (in_array($item->reference(), $this->references())) {
                return $item->id();
            }
        }
        if (count($this->enveloppe()->murs())) {
            return current($this->enveloppe()->murs())->id();
        }
        return false;
    }

    /**
     * Si la référence à la paroi est rompue, on associe par défaut le pont thermique à la première paroi compatible
     */
    public function paroi_id(): null|Id|false
    {
        if (in_array($this->type_liaison(), [TypeLiaison::REFEND_MUR, TypeLiaison::PLANCHER_INTERMEDIAIRE_MUR])) {
            return null;
        }
        if (\in_array($this->type_liaison(), [TypeLiaison::MENUISERIE_MUR])) {
            foreach ($this->enveloppe()->baies() as $item) {
                if (in_array($item->reference(), $this->references())) {
                    return $item->id();
                }
            }
            foreach ($this->enveloppe()->portes() as $item) {
                if (in_array($item->reference(), $this->references())) {
                    return $item->id();
                }
            }
            if (count($this->enveloppe()->baies())) {
                return current($this->enveloppe()->baies())->id();
            }
            if (count($this->enveloppe()->portes())) {
                return current($this->enveloppe()->portes())->id();
            }
            return false;
        }
        foreach ($this->enveloppe()->planchers_bas() as $item) {
            if (in_array($item->reference(), $this->references())) {
                return $item->id();
            }
        }
        foreach ($this->enveloppe()->planchers_hauts() as $item) {
            if (in_array($item->reference(), $this->references())) {
                return $item->id();
            }
        }
        if (count($this->enveloppe()->planchers_bas())) {
            return current($this->enveloppe()->planchers_bas())->id();
        }
        if (count($this->enveloppe()->planchers_hauts())) {
            return current($this->enveloppe()->planchers_hauts())->id();
        }

        return false;
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
}
