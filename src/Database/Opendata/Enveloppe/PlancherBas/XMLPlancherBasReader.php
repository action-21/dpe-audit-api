<?php

namespace App\Database\Opendata\Enveloppe\PlancherBas;

use App\Database\Opendata\Enveloppe\XMLParoiReader;
use App\Domain\Common\ValueObject\Annee;
use App\Domain\Enveloppe\Enum\{EtatIsolation, InertieParoi, TypeIsolation};
use App\Domain\Enveloppe\Enum\PlancherBas\{TypePlancherBas};
use App\Domain\Enveloppe\ValueObject\Isolation;

final class XMLPlancherBasReader extends XMLParoiReader
{
    public function supports(): bool
    {
        return $this->surface() > 0;
    }

    public function type_structure(): ?TypePlancherBas
    {
        return TypePlancherBas::from_enum_type_plancher_bas_id($this->enum_type_plancher_bas_id());
    }

    public function annee_construction(): ?Annee
    {
        return null;
    }

    public function annee_renovation(): ?Annee
    {
        return null;
    }

    public function perimetre(): float
    {
        return $this->findOne('.//perimetre_ue')?->floatval()
            ?? \sqrt($this->findOneOrError('.//surface_paroi_opaque')->floatval()) * 4;
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
        return $this->findOneOrError('//inertie_plancher_bas_lourd')->boolval()
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

    public function upb0_saisi(): ?float
    {
        return $this->findOne('.//upb0_saisi')?->floatval();
    }

    public function upb_saisi(): ?float
    {
        return $this->findOne('.//upb_saisi')?->floatval();
    }

    public function enum_orientation_id(): int
    {
        return $this->findOneOrError('.//enum_orientation_id')->intval();
    }

    public function enum_type_plancher_bas_id(): int
    {
        return $this->findOneOrError('.//enum_type_plancher_bas_id')->intval();
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
