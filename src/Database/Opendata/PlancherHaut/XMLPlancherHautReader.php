<?php

namespace App\Database\Opendata\PlancherHaut;

use App\Database\Opendata\{XMLElement, XMLReaderIterator};
use App\Domain\Common\Enum\Orientation;
use App\Domain\Common\Identifier\Reference;
use App\Domain\PlancherHaut\Enum\Inertie;
use App\Domain\PlancherHaut\ValueObject\{Caracteristique, Surface, Uph, Uph0};
use App\Domain\Paroi\Enum\{PeriodeIsolation, TypeIsolation};
use App\Domain\Paroi\ValueObject\{AnneeIsolation, EpaisseurIsolant, Isolation, OrientationParoi, ResistanceIsolant};
use App\Domain\PlancherHaut\Enum\Mitoyennete;
use App\Domain\PlancherHaut\Enum\TypePlancherHaut;

final class XMLPlancherHautReader extends XMLReaderIterator
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
        return $this->get()->findOne('.//description')?->getValue() ?? "Plancher haut non décrit";
    }

    public function surface_aue(): ?float
    {
        return ($value = $this->get()->findOne('.//surface_aue')?->getValue()) ? (float) $value : null;
    }

    public function enum_type_adjacence_id(): int
    {
        return (int) $this->get()->findOneOrError('.//enum_type_adjacence_id')->getValue();
    }

    public function enum_type_plancher_haut_id(): int
    {
        return (int) $this->get()->findOneOrError('.//enum_type_plancher_haut_id')->getValue();
    }

    public function type_plancher_haut(): TypePlancherHaut
    {
        return TypePlancherHaut::from_enum_type_plancher_haut_id($this->enum_type_plancher_haut_id());
    }

    public function surface_paroi_opaque(): Surface
    {
        return Surface::from($this->get()->findOneOrError('.//surface_paroi_opaque')->getValue());
    }

    public function enum_type_isolation_id(): int
    {
        return (int) $this->get()->findOneOrError('.//enum_type_isolation_id')->getValue();
    }

    public function type_isolation(): TypeIsolation
    {
        return TypeIsolation::from_enum_type_isolation_id($this->enum_type_isolation_id());
    }

    public function enum_periode_isolation_id(): ?int
    {
        return ($value = $this->get()->findOne('.//enum_periode_isolation_id')?->getValue()) ? (int) $value : null;
    }

    public function periode_isolation(): ?PeriodeIsolation
    {
        return $this->enum_periode_isolation_id() ? PeriodeIsolation::from_enum_periode_isolation_id($this->enum_periode_isolation_id()) : null;
    }

    public function epaisseur_isolation(): ?EpaisseurIsolant
    {
        return ($value = $this->get()->findOne('.//epaisseur_isolation')?->getValue()) ? EpaisseurIsolant::from((float) $value * 10) : null;
    }

    public function resistance_isolation(): ?ResistanceIsolant
    {
        return ($value = $this->get()->findOne('.//resistance_isolation')?->getValue()) ? ResistanceIsolant::from((float) $value) : null;
    }

    public function uph0_saisi(): ?Uph0
    {
        return ($value = $this->get()->findOne('.//ph0_saisi')?->getValue()) ? Uph0::from((float) $value) : null;
    }

    public function uph_saisi(): ?Uph
    {
        return ($value = $this->get()->findOne('.//ph_saisi')?->getValue()) ? Uph::from((float) $value) : null;
    }

    // Données déduites

    public function mitoyennete(): Mitoyennete
    {
        return Mitoyennete::from_type_adjacence_id($this->enum_type_adjacence_id());
    }

    public function orientation(): ?OrientationParoi
    {
        $reference = $this->reference();
        foreach ($this->get()->search('//baie_vitree_collection/baie_vitree') as $xml) {
            if ($reference !== $xml->findOne('.//reference_paroi')?->getValue()) {
                continue;
            }
            if (null === $enum_orientation_id = $xml->findOne('.//enum_orientation_id')?->getValue()) {
                continue;
            }
            return ($orientation = Orientation::try_from_enum_orientation_id((int) $enum_orientation_id))
                ? OrientationParoi::from($orientation->to_azimut())
                : null;
        }
        return null;
    }

    public function surface_paroi_totale(): Surface
    {
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

    public function inertie(): Inertie
    {
        return (bool)(int) $this->get()->findOneOrError('//inertie_plancher_haut_lourd')->getValue() ? Inertie::LOURDE : Inertie::LEGERE;
    }

    public function annee_isolation(): ?AnneeIsolation
    {
        return $this->periode_isolation() ? AnneeIsolation::from($this->periode_isolation()->to_int()) : null;
    }

    public function caracteristique(): Caracteristique
    {
        return new Caracteristique(
            surface: $this->surface_paroi_totale(),
            type_plancher_haut: $this->type_plancher_haut(),
            inertie: $this->inertie(),
            uph0: $this->uph0_saisi(),
            uph: $this->uph_saisi(),
        );
    }

    public function isolation(): Isolation
    {
        return new Isolation(
            type_isolation: $this->type_isolation(),
            annnee_isolation: $this->annee_isolation(),
            epaisseur_isolant: $this->epaisseur_isolation(),
            resistance_thermique: $this->resistance_isolation(),
        );
    }

    // Données intermédiaires

    public function uph0(): ?Uph0
    {
        return ($value = $this->get()->findOne('.//uph0')?->getValue()) ? Uph0::from((float) $value) : null;
    }

    public function uph(): Uph
    {
        return Uph::from((float) $this->get()->findOneOrError('.//uph')->getValue());
    }

    public function b(): float
    {
        return (float) $this->get()->findOneOrError('.//b')->getValue();
    }

    public function read(XMLElement $xml): self
    {
        $xml = $xml->findOneOfOrError(['/audit/logement_collection//logement[.//enum_scenario_id="0"]', '/dpe/logement']);
        $this->array = $xml->findMany('.//plancher_haut_collection//plancher_haut');
        return $this;
    }
}
