<?php

namespace App\Database\Opendata\Enveloppe\PlancherHaut;

use App\Database\Opendata\Enveloppe\XMLParoiReader;
use App\Domain\Common\ValueObject\{Annee, Orientation};
use App\Domain\Enveloppe\Enum\{EtatIsolation, InertieParoi, TypeIsolation};
use App\Domain\Enveloppe\Enum\PlancherHaut\{Configuration, TypePlancherHaut};
use App\Domain\Enveloppe\ValueObject\Isolation;

final class XMLPlancherHautReader extends XMLParoiReader
{
    public function supports(): bool
    {
        return $this->surface() > 0;
    }

    public function configuration(): Configuration
    {
        if ($this->type_structure() && $value = Configuration::from_type_plancher_haut($this->type_structure())) {
            return $value;
        }
        return match ($this->enum_type_adjacence_id()) {
            11, 12, 13 => Configuration::COMBLES_PERDUS,
            default => Configuration::TERRASSE,
        };
    }

    public function annee_construction(): ?Annee
    {
        return null;
    }

    public function annee_renovation(): ?Annee
    {
        return null;
    }

    public function orientation(): ?Orientation
    {
        return null;
    }

    public function type_structure(): ?TypePlancherHaut
    {
        return TypePlancherHaut::from_enum_type_plancher_haut_id($this->enum_type_plancher_haut_id());
    }

    public function surface(): float
    {
        $surface = $this->findOneOrError('.//surface_paroi_opaque')->floatval();

        foreach ($this->baies() as $item) {
            $surface += $item->surface();
        }
        foreach ($this->portes() as $item) {
            $surface += $item->surface();
        }
        return $surface;
    }

    public function inertie(): InertieParoi
    {
        return $this->findOneOrError('//inertie_plancher_haut_lourd')->boolval()
            ? InertieParoi::LOURDE
            : InertieParoi::LEGERE;
    }

    public function etat_isolation(): ?EtatIsolation
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
            1 => Annee::from(1947),
            2 => Annee::from(1974),
            3 => Annee::from(1977),
            4 => Annee::from(1982),
            5 => Annee::from(1988),
            6 => Annee::from(2000),
            7 => Annee::from(2005),
            8 => Annee::from(2012),
            9 => Annee::from(2021),
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

    public function uph0_saisi(): ?float
    {
        return $this->findOne('.//uph0_saisi')?->floatval();
    }

    public function uph_saisi(): ?float
    {
        return $this->findOne('.//uph_saisi')?->floatval();
    }

    public function enum_orientation_id(): int
    {
        return $this->findOneOrError('.//enum_orientation_id')->intval();
    }

    public function enum_type_plancher_haut_id(): int
    {
        return $this->findOneOrError('.//enum_type_plancher_haut_id')->intval();
    }

    public function enum_type_isolation_id(): int
    {
        return $this->findOneOrError('.//enum_type_isolation_id')->intval();
    }

    public function isolation(): Isolation
    {
        return Isolation::create(
            etat_isolation: $this->etat_isolation(),
            type_isolation: $this->type_isolation(),
            annee_isolation: $this->annee_isolation(),
            epaisseur_isolation: $this->epaisseur_isolation(),
            resistance_thermique_isolation: $this->resistance_thermique_isolation(),
        );
    }
}
