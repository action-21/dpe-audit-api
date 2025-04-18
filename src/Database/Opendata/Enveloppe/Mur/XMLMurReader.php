<?php

namespace App\Database\Opendata\Enveloppe\Mur;

use App\Database\Opendata\Enveloppe\Baie\XMLBaieReader;
use App\Database\Opendata\Enveloppe\Porte\XMLPorteReader;
use App\Database\Opendata\Enveloppe\XMLParoiReader;
use App\Domain\Common\ValueObject\{Annee, Orientation};
use App\Domain\Enveloppe\Enum\{EtatIsolation, InertieParoi, TypeIsolation};
use App\Domain\Enveloppe\Enum\Mur\{TypeDoublage, TypeMur};

final class XMLMurReader extends XMLParoiReader
{
    /**
     * @return XMLBaieReader[]
     */
    public function baies(): array
    {
        return array_filter(
            $this->enveloppe()->baies(),
            fn(XMLBaieReader $reader) => $reader->match($this->identifiants())
        );
    }

    /**
     * @return XMLPorteReader[]
     */
    public function portes(): array
    {
        return array_filter(
            $this->enveloppe()->portes(),
            fn(XMLPorteReader $reader) => $reader->match($this->identifiants())
        );
    }

    public function annee_construction(): ?Annee
    {
        return null;
    }

    public function annee_renovation(): ?Annee
    {
        return null;
    }

    public function orientation(): Orientation
    {
        return Orientation::from(
            $this->findOneOrError('.//enum_orientation_id')->intval()
        );
    }

    public function type_structure(): TypeMur
    {
        return TypeMur::from_enum_materiaux_structure_id($this->enum_materiaux_structure_mur_id());
    }

    public function type_doublage(): TypeDoublage
    {
        return TypeDoublage::from_enum_type_doublage_id($this->enum_type_doublage_id());
    }

    public function presence_enduit_isolant(): bool
    {
        return $this->findOne('.//enduit_isolant_paroi_ancienne')?->boolval() ?? false;
    }

    public function paroi_ancienne(): bool
    {
        return $this->findOne('.//paroi_ancienne')?->boolval() ?? false;
    }

    public function epaisseur_structure(): ?float
    {
        return $this->findOne('.//epaisseur_structure')?->floatval();
    }

    public function surface(): float
    {
        if (null === $surface = $this->surface_paroi_totale()) {
            $surface = $this->surface_paroi_opaque();

            foreach ($this->baies() as $paroi) {
                $surface += $paroi->surface();
            }
            foreach ($this->portes() as $paroi) {
                $surface += $paroi->surface();
            }
            return $surface;
        }
        return $surface;
    }

    public function surface_paroi_totale(): ?float
    {
        return $this->findOne('.//surface_paroi_totale')?->floatval();
    }

    public function surface_paroi_opaque(): float
    {
        return $this->findOneOrError('.//surface_paroi_opaque')->floatval();
    }

    public function inertie(): InertieParoi
    {
        return $this->findOneOrError('//inertie_paroi_verticale_lourd')->boolval()
            ? InertieParoi::LOURDE
            : InertieParoi::LEGERE;
    }

    public function etat_isolation(): EtatIsolation
    {
        return EtatIsolation::from_enum_type_isolation_id($this->enum_type_isolation_id());
    }

    public function type_isolation(): ?TypeIsolation
    {
        return TypeIsolation::from_enum_type_isolation_id($this->enum_type_isolation_id());
    }

    public function annee_isolation(): ?Annee
    {
        return match ($this->findOne('.//enum_periode_isolation_id')?->intval()) {
            1 => Annee::from(1945),
            2 => Annee::from(1961),
            3 => Annee::from(1976),
            4 => Annee::from(1980),
            5 => Annee::from(1985),
            6 => Annee::from(1995),
            7 => Annee::from(2003),
            8 => Annee::from(2009),
            9 => Annee::from(2017),
            10 => $this->audit()->annee_etablissement(),
            default => null,
        };
    }

    public function epaisseur_isolation(): ?int
    {
        return ($value = $this->findOne('.//epaisseur_isolation')?->intval()) ? $value * 10 : null;
    }

    public function resistance_thermique_isolation(): ?float
    {
        return $this->findOne('.//resistance_isolation')?->floatval();
    }

    public function umur0_saisi(): ?float
    {
        return $this->findOne('.//umur0_saisi')?->floatval();
    }

    public function umur_saisi(): ?float
    {
        return $this->findOne('.//umur_saisi')?->floatval();
    }

    public function enum_orientation_id(): int
    {
        return $this->findOneOrError('.//enum_orientation_id')->intval();
    }

    public function enum_materiaux_structure_mur_id(): int
    {
        return $this->findOneOrError('.//enum_materiaux_structure_mur_id')->intval();
    }

    public function enum_type_doublage_id(): int
    {
        return $this->findOneOrError('.//enum_type_doublage_id')->intval();
    }

    public function enum_type_isolation_id(): int
    {
        return $this->findOneOrError('.//enum_type_isolation_id')->intval();
    }

    // Données intermédiaires

    public function umur0(): ?float
    {
        return $this->findOne('.//umur0')?->floatval();
    }

    public function umur(): float
    {
        return $this->findOneOrError('.//umur')->floatval();
    }

    public function b(): float
    {
        return $this->findOneOrError('.//b')->floatval();
    }
}
