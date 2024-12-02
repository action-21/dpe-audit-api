<?php

namespace App\Database\Opendata\Mur;

use App\Database\Opendata\XMLReaderIterator;
use App\Domain\Common\Type\Id;
use App\Domain\Mur\Enum\{EtatIsolation, Inertie, Mitoyennete, TypeDoublage, TypeIsolation, TypeMur};

final class XMLMurReader extends XMLReaderIterator
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
        return $this->xml()->findOne('.//description')?->strval() ?? 'Mur non décrit';
    }

    public function orientation(): float
    {
        return $this->xml()->findOneOrError('.//enum_orientation_id')->orientation();
    }

    public function mitoyennete(): Mitoyennete
    {
        return $this->enum_cfg_isolation_lnc_id() === 1
            ? Mitoyennete::LOCAL_NON_ACCESSIBLE
            : Mitoyennete::from_type_adjacence_id($this->enum_type_adjacence_id());
    }

    public function type_mur(): TypeMur
    {
        return TypeMur::from_enum_materiaux_structure_id($this->enum_materiaux_structure_mur_id());
    }

    public function type_doublage(): TypeDoublage
    {
        return TypeDoublage::from_enum_type_doublage_id($this->enum_type_doublage_id());
    }

    public function presence_enduit_isolant(): bool
    {
        return $this->xml()->findOne('.//enduit_isolant_paroi_ancienne')?->boolval() ?? false;
    }

    public function paroi_ancienne(): bool
    {
        return $this->xml()->findOne('.//paroi_ancienne')?->boolval() ?? false;
    }

    public function epaisseur(): ?float
    {
        return $this->xml()->findOne('.//epaisseur_structure')?->floatval();
    }

    public function surface(): float
    {
        if ($value = $this->xml()->findOne('.//surface_paroi_totale')?->floatval())
            return $value;

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
        return $this->xml()->findOneOrError('//inertie_paroi_verticale_lourd')->boolval() ? Inertie::LOURDE : Inertie::LEGERE;
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

    public function umur0_saisi(): ?float
    {
        return $this->xml()->findOne('.//umur0_saisi')?->floatval();
    }

    public function umur_saisi(): ?float
    {
        return $this->xml()->findOne('.//umur_saisi')?->floatval();
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

    public function enum_materiaux_structure_mur_id(): int
    {
        return $this->xml()->findOneOrError('.//enum_materiaux_structure_mur_id')->intval();
    }

    public function enum_type_doublage_id(): int
    {
        return $this->xml()->findOneOrError('.//enum_type_doublage_id')->intval();
    }

    public function enum_type_isolation_id(): int
    {
        return $this->xml()->findOneOrError('.//enum_type_isolation_id')->intval();
    }

    // Données intermédiaires

    public function umur0(): ?float
    {
        return $this->xml()->findOne('.//umur0')?->floatval();
    }

    public function umur(): float
    {
        return $this->xml()->findOneOrError('.//umur')->floatval();
    }

    public function b(): float
    {
        return $this->xml()->findOneOrError('.//b')->floatval();
    }
}
