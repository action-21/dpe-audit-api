<?php

namespace App\Database\Opendata\Lnc;

use App\Database\Opendata\{XMLElement, XMLReaderIterator};
use App\Domain\Common\Enum\Orientation;
use App\Domain\Common\Identifier\Reference;
use App\Domain\Lnc\Enum\{InclinaisonVitrage as EnumInclinaisonVitrage, NatureMenuiserie, TypeVitrage};
use App\Domain\Lnc\Table\{T, TRepository};
use App\Domain\Lnc\ValueObject\{InclinaisonVitrage, OrientationBaie, SurfaceParoi};

final class XMLEtsBaieReader extends XMLReaderIterator
{
    private T $table_t;

    public function __construct(private TRepository $table_t_repository,)
    {
    }

    public function id(): \Stringable
    {
        return Reference::create($this->reference());
    }

    public function reference(): string
    {
        return $this->get()->findOneOrError('.//reference')->getValue();
    }

    public function description(): string
    {
        return $this->get()->findOne('.//description')?->getValue() ?? "Baie non décrite";
    }

    public function enum_orientation_id(): int
    {
        return (int) $this->get()->findOneOrError('.//enum_orientation_id')->getValue();
    }

    public function enum_orientation(): Orientation
    {
        return Orientation::from_enum_orientation_id($this->enum_orientation_id());
    }

    public function enum_inclinaison_vitrage_id(): int
    {
        return (int) $this->get()->findOneOrError('.//enum_inclinaison_vitrage_id')->getValue();
    }

    public function enum_inclinaison_vitrage(): EnumInclinaisonVitrage
    {
        return EnumInclinaisonVitrage::from_enum_inclinaison_vitrage_id($this->enum_inclinaison_vitrage_id());
    }

    public function nombre(): int
    {
        return \max((int) $this->get()->findOneOrError('.//nb_baie')->getValue(), 1);
    }

    public function surface_totale(): SurfaceParoi
    {
        return SurfaceParoi::from((float) $this->get()->findOneOrError('.//surface_totale_baie')->getValue());
    }

    // Données déduites

    public function surface(): SurfaceParoi
    {
        return SurfaceParoi::from($this->surface_totale()->valeur() / $this->nombre());
    }

    public function orientation(): OrientationBaie
    {
        return OrientationBaie::from($this->enum_orientation()->to_azimut());
    }

    public function inclinaison_vitrage(): InclinaisonVitrage
    {
        return InclinaisonVitrage::from($this->enum_inclinaison_vitrage()->to_int());
    }

    public function enum_nature_menuiserie(): NatureMenuiserie
    {
        return $this->table_t->nature_menuiserie;
    }

    public function enum_type_vitrage(): ?TypeVitrage
    {
        return $this->table_t->type_vitrage;
    }

    public function vitrage_vir(): ?bool
    {
        return $this->table_t->vitrage_vir;
    }

    public function read(XMLElement $xml, XMLEtsReader $context): self
    {
        $this->array = $xml->findMany('.//baie_ets_collection/baie_ets');
        $table_t_collection = $this->table_t_repository->search_by(
            tv_coef_transparence_ets_id: $context->tv_coef_transparence_ets_id()
        );
        if (0 === $table_t_collection->count()) {
            throw new \Exception("Table T introuvable pour l'id {$context->tv_coef_transparence_ets_id()}");
        }
        $this->table_t = $table_t_collection->first();
        return $this;
    }
}
