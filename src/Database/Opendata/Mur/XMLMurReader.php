<?php

namespace App\Database\Opendata\Mur;

use App\Database\Opendata\{XMLElement, XMLReaderIterator};
use App\Domain\Common\Enum\Orientation;
use App\Domain\Common\Identifier\Reference;
use App\Domain\Mur\Enum\{Inertie, MateriauxStructure, Mitoyennete, TypeDoublage};
use App\Domain\Mur\ValueObject\{Caracteristique, Epaisseur, Surface, Umur, Umur0};
use App\Domain\Paroi\Enum\{PeriodeIsolation, TypeIsolation};
use App\Domain\Paroi\ValueObject\{AnneeIsolation, EpaisseurIsolant, Isolation, OrientationParoi, ResistanceIsolant};

final class XMLMurReader extends XMLReaderIterator
{
    public function id(): \Stringable
    {
        return Reference::create($this->reference());
    }

    public function reference(): string
    {
        return $this->get()->findOneOrError('.//reference')->getValue();
    }

    public function reference_lnc(): ?string
    {
        return $this->get()->findOne('.//reference_lnc')?->getValue();
    }

    public function description(): string
    {
        return $this->get()->findOne('.//description')?->getValue() ?? "Mur non décrit";
    }

    public function surface_aue(): ?float
    {
        return ($value = $this->get()->findOne('.//surface_aue')?->getValue()) ? (float) $value : null;
    }

    public function enum_type_adjacence_id(): int
    {
        return (int) $this->get()->findOneOrError('.//enum_type_adjacence_id')->getValue();
    }

    public function enum_orientation_id(): int
    {
        return (int) $this->get()->findOneOrError('.//enum_orientation_id')->getValue();
    }

    public function enum_orientation(): Orientation
    {
        return Orientation::from_enum_orientation_id($this->enum_orientation_id());
    }

    public function enum_materiaux_structure_mur_id(): int
    {
        return (int) $this->get()->findOneOrError('.//enum_materiaux_structure_mur_id')->getValue();
    }

    public function enum_materiaux_structure(): MateriauxStructure
    {
        return MateriauxStructure::from_enum_materiaux_structure_id($this->enum_materiaux_structure_mur_id());
    }

    public function enum_type_doublage_id(): int
    {
        return (int) $this->get()->findOneOrError('.//enum_type_doublage_id')->getValue();
    }

    public function enum_type_doublage(): TypeDoublage
    {
        return TypeDoublage::from_enum_type_doublage_id($this->enum_type_doublage_id());
    }

    public function enduit_isolant_paroi_ancienne(): bool
    {
        return (bool)(int) $this->get()->findOneOrError('.//enduit_isolant_paroi_ancienne')->getValue();
    }

    public function epaisseur(): ?Epaisseur
    {
        return ($value = $this->get()->findOne('.//epaisseur_structure')?->getValue()) ? Epaisseur::from((float) $value) : null;
    }

    public function surface_paroi_opaque(): Surface
    {
        return Surface::from($this->get()->findOneOrError('.//surface_paroi_opaque')->getValue());
    }

    public function enum_type_isolation_id(): int
    {
        return (int) $this->get()->findOneOrError('.//enum_type_isolation_id')->getValue();
    }

    public function enum_type_isolation(): TypeIsolation
    {
        return TypeIsolation::from_enum_type_isolation_id($this->enum_type_isolation_id());
    }

    public function enum_periode_isolation_id(): ?int
    {
        return ($value = $this->get()->findOne('.//enum_periode_isolation_id')?->getValue()) ? (int) $value : null;
    }

    public function enum_periode_isolation(): ?PeriodeIsolation
    {
        return ($id = $this->enum_periode_isolation_id()) ? PeriodeIsolation::from_enum_periode_isolation_id($id) : null;
    }

    public function epaisseur_isolation(): ?EpaisseurIsolant
    {
        return ($value = $this->get()->findOne('.//epaisseur_isolation')?->getValue()) ? EpaisseurIsolant::from((float) $value * 10) : null;
    }

    public function resistance_isolation(): ?ResistanceIsolant
    {
        return ($value = $this->get()->findOne('.//resistance_isolation')?->getValue()) ? ResistanceIsolant::from((float) $value) : null;
    }

    public function umur0_saisi(): ?Umur0
    {
        return ($value = $this->get()->findOne('.//umur0_saisi')?->getValue()) ? Umur0::from((float) $value) : null;
    }

    public function umur_saisi(): ?Umur
    {
        return ($value = $this->get()->findOne('.//umur_saisi')?->getValue()) ? Umur::from((float) $value) : null;
    }

    // Données intermédiaires

    public function umur0(): ?Umur0
    {
        return ($value = $this->get()->findOne('.//umur0')?->getValue()) ? Umur0::from((float) $value) : null;
    }

    public function umur(): Umur
    {
        return Umur::from((float) $this->get()->findOneOrError('.//umur')->getValue());
    }

    public function b(): float
    {
        return (float) $this->get()->findOneOrError('.//b')->getValue();
    }

    // Données déduites

    public function mitoyennete(): Mitoyennete
    {
        return Mitoyennete::from_type_adjacence_id($this->enum_type_adjacence_id());
    }

    public function orientation(): OrientationParoi
    {
        return OrientationParoi::from($this->enum_orientation()->to_azimut());
    }

    public function enduit_isolant(): bool
    {
        return $this->enduit_isolant_paroi_ancienne();
    }

    public function paroi_ancienne(): bool
    {
        return $this->enduit_isolant_paroi_ancienne();
    }

    public function inertie(): Inertie
    {
        return (bool)(int) $this->get()->findOneOrError('//inertie_paroi_verticale_lourd')->getValue() ? Inertie::LOURDE : Inertie::LEGERE;
    }

    public function surface_paroi_totale(): Surface
    {
        if ($value = $this->get()->findOne('.//surface_paroi_totale')?->getValue()) {
            return Surface::from((float) $value);
        }
        $reference = $this->reference();
        $surface = $this->surface_paroi_opaque()->valeur();

        foreach ($this->get()->search('//baie_vitree_collection/baie_vitree') as $item) {
            if ($item->findOne('.//reference_paroi')?->getValue() !== $reference) {
                continue;
            }
            $surface += (float) $item->findOneOrError('.//surface_totale_baie')->getValue();
        }
        foreach ($this->get()->search('//porte_collection/porte') as $item) {
            if ($item->findOne('.//reference_paroi')?->getValue() !== $reference) {
                continue;
            }
            $surface += (float) $item->findOneOrError('.//surface_porte')->getValue();
        }
        return Surface::from($surface);
    }

    public function annee_isolation(): ?AnneeIsolation
    {
        return $this->enum_periode_isolation() ? AnneeIsolation::from($this->enum_periode_isolation()->to_int()) : null;
    }

    public function caracteristique(): Caracteristique
    {
        return new Caracteristique(
            materiaux_structure: $this->enum_materiaux_structure(),
            epaisseur_structure: $this->epaisseur(),
            type_doublage: $this->enum_type_doublage(),
            surface: $this->surface_paroi_totale(),
            enduit_isolant: $this->enduit_isolant(),
            paroi_ancienne: $this->paroi_ancienne(),
            inertie: $this->inertie(),
            umur0: $this->umur0_saisi(),
            umur: $this->umur_saisi(),
        );
    }

    public function isolation(): Isolation
    {
        return new Isolation(
            type_isolation: $this->enum_type_isolation(),
            annnee_isolation: $this->annee_isolation(),
            epaisseur_isolant: $this->epaisseur_isolation(),
            resistance_thermique: $this->resistance_isolation(),
        );
    }

    public function read(XMLElement $xml): self
    {
        $xml = $xml->findOneOfOrError(['/audit/logement_collection//logement[.//enum_scenario_id="0"]', '/dpe/logement']);
        $this->array = $xml->findMany('.//mur_collection//mur');
        return $this;
    }
}
