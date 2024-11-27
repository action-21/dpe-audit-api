<?php

namespace App\Database\Opendata\PlancherBas;

use App\Database\Opendata\XMLReaderIterator;
use App\Domain\Common\Type\Id;
use App\Domain\PlancherBas\Enum\{EtatIsolation, Inertie, Mitoyennete, TypeIsolation, TypePlancherBas};

final class XMLPlancherBasReader extends XMLReaderIterator
{
    public function id(): Id
    {
        return $this->xml()->findOneOrError('.//reference')->id();
    }

    public function description(): string
    {
        return $this->xml()->findOne('.//description')?->strval() ?? 'Plancher bas non décrit';
    }

    public function mitoyennete(): Mitoyennete
    {
        return Mitoyennete::from_type_adjacence_id($this->enum_type_adjacence_id());
    }

    public function type_plancher_bas(): TypePlancherBas
    {
        return TypePlancherBas::from_enum_type_plancher_bas_id($this->enum_type_plancher_bas_id());
    }

    public function perimetre(): float
    {
        return $this->xml()->findOne('.//perimetre_ue')?->floatval()
            ?? \sqrt($this->xml()->findOneOrError('.//surface_paroi_opaque')->floatval()) * 4;
    }

    public function surface(): float
    {
        $reference = $this->reference();
        $surface = $this->xml()->findOneOrError('.//surface_paroi_opaque')->floatval();

        foreach ($this->xml()->baie_collection() as $item) {
            if ($item->findOne('.//reference_paroi')?->strval() === $reference)
                $surface += $item->findOneOrError('.//surface_totale_baie')->floatval();
        }
        foreach ($this->xml()->porte_collection() as $item) {
            if ($item->findOne('.//reference_paroi')?->strval() === $reference)
                $surface += $item->findOneOrError('.//surface_porte')->floatval();
        }
        return $surface;
    }

    public function inertie(): Inertie
    {
        return $this->xml()->findOneOrError('//inertie_plancher_bas_lourd')->boolval() ? Inertie::LOURDE : Inertie::LEGERE;
    }

    public function etat_isolation(): EtatIsolation
    {
        return EtatIsolation::from_enum_type_isolation_id($this->enum_type_isolation_id());
    }

    public function type_isolation(): ?TypeIsolation
    {
        return TypeIsolation::from_enum_type_isolation_id($this->enum_type_isolation_id());
    }

    public function annee_isolation(): ?int
    {
        return $this->xml()->findOne('.//enum_periode_isolation_id')?->annee_isolation();
    }

    public function epaisseur_isolation(): ?int
    {
        return ($value = $this->xml()->findOne('.//epaisseur_isolation')?->intval()) ? $value * 10 : null;
    }

    public function resistance_isolation(): ?float
    {
        return $this->xml()->findOne('.//resistance_isolation')?->floatval();
    }

    public function upb0_saisi(): ?float
    {
        return $this->xml()->findOne('.//upb0_saisi')?->floatval();
    }

    public function upb_saisi(): ?float
    {
        return $this->xml()->findOne('.//upb_saisi')?->floatval();
    }

    public function reference(): string
    {
        return $this->xml()->findOneOrError('.//reference')->strval();
    }

    public function enum_type_adjacence_id(): int
    {
        return $this->xml()->findOneOrError('.//enum_type_adjacence_id')->intval();
    }

    public function enum_orientation_id(): int
    {
        return $this->xml()->findOneOrError('.//enum_orientation_id')->intval();
    }

    public function enum_type_plancher_bas_id(): int
    {
        return $this->xml()->findOneOrError('.//enum_type_plancher_bas_id')->intval();
    }

    public function enum_type_isolation_id(): int
    {
        return $this->xml()->findOneOrError('.//enum_type_isolation_id')->intval();
    }

    // Données intermédiaires

    public function upb0(): ?float
    {
        return $this->xml()->findOne('.//upb0')?->floatval();
    }

    public function upb(): float
    {
        return $this->xml()->findOneOrError('.//upb')->floatval();
    }

    public function b(): float
    {
        return $this->xml()->findOneOrError('.//b')->floatval();
    }
}
