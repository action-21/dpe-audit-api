<?php

namespace App\Database\Opendata\PlancherBas;

use App\Database\Opendata\{XMLElement, XMLReaderIterator};
use App\Domain\Common\Identifier\Reference;
use App\Domain\PlancherBas\Enum\Inertie;
use App\Domain\PlancherBas\ValueObject\{Caracteristique, Perimetre, Surface, Upb, Upb0};
use App\Domain\Paroi\Enum\{PeriodeIsolation, TypeIsolation};
use App\Domain\Paroi\ValueObject\{AnneeIsolation, EpaisseurIsolant, Isolation, ResistanceIsolant};
use App\Domain\PlancherBas\Enum\Mitoyennete;
use App\Domain\PlancherBas\Enum\TypePlancherBas;

final class XMLPlancherBasReader extends XMLReaderIterator
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
        return $this->get()->findOne('.//description')?->getValue() ?? "Plancher bas non décrit";
    }

    public function surface_aue(): ?float
    {
        return ($value = $this->get()->findOne('.//surface_aue')?->getValue()) ? (float) $value : null;
    }

    public function enum_type_adjacence_id(): int
    {
        return (int) $this->get()->findOneOrError('.//enum_type_adjacence_id')->getValue();
    }

    public function enum_type_plancher_bas_id(): int
    {
        return (int) $this->get()->findOneOrError('.//enum_type_plancher_bas_id')->getValue();
    }

    public function type_plancher_bas(): TypePlancherBas
    {
        return TypePlancherBas::from_enum_type_plancher_bas_id($this->enum_type_plancher_bas_id());
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

    public function upb0_saisi(): ?Upb0
    {
        return ($value = $this->get()->findOne('.//pb0_saisi')?->getValue()) ? Upb0::from((float) $value) : null;
    }

    public function upb_saisi(): ?Upb
    {
        return ($value = $this->get()->findOne('.//pb_saisi')?->getValue()) ? Upb::from((float) $value) : null;
    }

    // Données déduites

    public function mitoyennete(): Mitoyennete
    {
        return Mitoyennete::from_type_adjacence_id($this->enum_type_adjacence_id());
    }

    public function inertie(): Inertie
    {
        return (bool)(int) $this->get()->findOneOrError('//inertie_plancher_bas_lourd')->getValue() ? Inertie::LOURDE : Inertie::LEGERE;
    }

    public function perimetre(): Perimetre
    {
        return ($value = $this->get()->findOne('.//perimetre')?->getValue())
            ? Perimetre::from((float) $value)
            : Perimetre::from(\sqrt($this->surface_paroi_totale()->valeur()) * 4);
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

    public function annee_isolation(): ?AnneeIsolation
    {
        return $this->periode_isolation() ? AnneeIsolation::from($this->periode_isolation()->to_int()) : null;
    }

    public function caracteristique(): Caracteristique
    {
        return new Caracteristique(
            surface: $this->surface_paroi_totale(),
            perimetre: $this->perimetre(),
            type_plancher_bas: $this->type_plancher_bas(),
            inertie: $this->inertie(),
            upb0: $this->upb0_saisi(),
            upb: $this->upb_saisi(),
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

    public function upb0(): ?Upb0
    {
        return ($value = $this->get()->findOne('.//upb0')?->getValue()) ? Upb0::from((float) $value) : null;
    }

    public function upb(): Upb
    {
        return Upb::from((float) $this->get()->findOneOrError('.//upb')->getValue());
    }

    public function b(): float
    {
        return (float) $this->get()->findOneOrError('.//b')->getValue();
    }

    public function ue(): float
    {
        return ($value = $this->get()->findOne('.//ue')?->getValue()) ? (float) $value : null;
    }

    public function read(XMLElement $xml): self
    {
        $xml = $xml->findOneOfOrError(['/audit/logement_collection//logement[.//enum_scenario_id="0"]', '/dpe/logement']);
        $this->array = $xml->findMany('.//plancher_bas_collection//plancher_bas');
        return $this;
    }
}
