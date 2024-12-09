<?php

namespace App\Database\Opendata\PlancherHaut;

use App\Database\Opendata\XMLReader;
use App\Domain\Common\Type\Id;
use App\Domain\PlancherHaut\Enum\{EtatIsolation, Inertie, Mitoyennete, TypeIsolation, TypePlancherHaut};
use App\Domain\PlancherHaut\ValueObject\{Caracteristique, Isolation};

final class XMLPlancherHautReader extends XMLReader
{
    public function match(string $reference): bool
    {
        return $reference === $this->reference() || $reference === $this->xml()->findOne('.//description')?->reference();
    }

    public function id(): Id
    {
        return $this->xml()->findOneOrError('.//reference')->id();
    }

    public function reference(): string
    {
        return $this->xml()->findOneOrError('.//reference')->reference();
    }

    public function description(): string
    {
        return $this->xml()->findOne('.//description')?->strval() ?? 'Plancher haut non décrit';
    }

    public function caracteristique(): Caracteristique
    {
        return new Caracteristique(
            type: $this->type_plancher_haut(),
            inertie: $this->inertie(),
            surface: $this->surface(),
            annee_construction: null,
            annee_renovation: null,
            u0: $this->uph0_saisi(),
            u: $this->uph_saisi(),
        );
    }

    public function isolation(): Isolation
    {
        return new Isolation(
            etat_isolation: $this->etat_isolation(),
            type_isolation: $this->type_isolation(),
            annee_isolation: $this->annee_isolation(),
            epaisseur_isolation: $this->epaisseur_isolation(),
            resistance_thermique_isolation: $this->resistance_isolation(),
        );
    }

    public function mitoyennete(): Mitoyennete
    {
        return $this->enum_cfg_isolation_lnc_id() === 1
            ? Mitoyennete::LOCAL_NON_ACCESSIBLE
            : Mitoyennete::from_type_adjacence_id($this->enum_type_adjacence_id());
    }

    public function type_plancher_haut(): TypePlancherHaut
    {
        return TypePlancherHaut::from_enum_type_plancher_haut_id($this->enum_type_plancher_haut_id());
    }

    public function surface(): float
    {
        $reference = $this->reference();
        $surface = $this->xml()->findOneOrError('.//surface_paroi_opaque')->floatval();

        foreach ($this->xml()->read_enveloppe()->read_baies() as $item) {
            if ($item->xml()->findOne('.//reference_paroi')?->strval() === $reference)
                $surface += $item->xml()->findOneOrError('.//surface_totale_baie')->floatval();
        }
        foreach ($this->xml()->read_enveloppe()->read_portes() as $item) {
            if ($item->xml()->findOne('.//reference_paroi')?->strval() === $reference)
                $surface += $item->xml()->findOneOrError('.//surface_porte')->floatval();
        }
        return $surface;
    }

    public function inertie(): Inertie
    {
        return $this->xml()->findOneOrError('//inertie_plancher_haut_lourd')->boolval() ? Inertie::LOURDE : Inertie::LEGERE;
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

    public function uph0_saisi(): ?float
    {
        return $this->xml()->findOne('.//uph0_saisi')?->floatval();
    }

    public function uph_saisi(): ?float
    {
        return $this->xml()->findOne('.//uph_saisi')?->floatval();
    }

    public function enum_type_adjacence_id(): int
    {
        return $this->xml()->findOneOrError('.//enum_type_adjacence_id')->intval();
    }

    public function enum_cfg_isolation_lnc_id(): ?int
    {
        return $this->xml()->findOne('.//enum_cfg_isolation_lnc_id')?->intval();
    }

    public function enum_orientation_id(): int
    {
        return $this->xml()->findOneOrError('.//enum_orientation_id')->intval();
    }

    public function enum_type_plancher_haut_id(): int
    {
        return $this->xml()->findOneOrError('.//enum_type_plancher_haut_id')->intval();
    }

    public function enum_type_isolation_id(): int
    {
        return $this->xml()->findOneOrError('.//enum_type_isolation_id')->intval();
    }

    // Données intermédiaires

    public function uph0(): ?float
    {
        return $this->xml()->findOne('.//uph0')?->floatval();
    }

    public function uph(): float
    {
        return $this->xml()->findOneOrError('.//uph')->floatval();
    }

    public function b(): float
    {
        return $this->xml()->findOneOrError('.//b')->floatval();
    }
}
