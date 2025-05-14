<?php

namespace App\Database\Opendata\Audit;

use App\Database\Opendata\{ObservatoireDPEAuditFinder, ObservatoireDPEAuditSearcher};
use App\Domain\Audit\{Audit, AuditCollection, AuditRepository};
use App\Domain\Audit\Enum\{Etiquette, TypeBatiment};
use App\Domain\Common\Enum\ZoneClimatique;
use App\Domain\Common\ValueObject\Id;
use App\Serializer\Opendata\{JSONAuditDeserializer, XMLAuditDeserializer};

final class OpendataAuditRepository implements AuditRepository
{
    public function __construct(
        private readonly ObservatoireDPEAuditFinder $finder,
        private readonly ObservatoireDPEAuditSearcher $searcher,
        private readonly XMLAuditDeserializer $xml_deserializer,
        private readonly JSONAuditDeserializer $json_denormalizer,
    ) {}

    public function find(Id $id): ?Audit
    {
        return ($xml = $this->finder->find($id)) ? $this->xml_deserializer->deserialize($xml) : null;
    }

    public function search(int $page = 1): AuditCollection
    {
        $this->searcher->addQuery('page', $page);
        $collection = [];

        foreach ($this->searcher->search() as $item) {
            $collection[] = $this->json_denormalizer->denormalize($item);
        }
        return new AuditCollection($collection);
    }

    public function count(): int
    {
        return $this->searcher->count();
    }

    public function sort(string $name): static
    {
        $this->searcher->addQuery('sort', $name);
        return $this;
    }

    public function randomize(): static
    {
        $this->searcher->randomize();
        return $this;
    }

    public function with_date_etablissement(?\DateTimeInterface $min = null, ?\DateTimeInterface $max = null): static
    {
        $min ? $this->searcher->addQuery('date_etablieement_dpe_gte', $min->format('Y-m-d')) : null;
        $max ? $this->searcher->addQuery('date_etablieement_dpe_lte', $max->format('Y-m-d')) : null;
        return $this;
    }

    public function with_surface_habitable(?float $min = null, ?float $max = null): static
    {
        $min ? $this->searcher->addQuery('surface_habitable_gte', $min) : null;
        $max ? $this->searcher->addQuery('surface_habitable_lte', $max) : null;
        return $this;
    }

    public function with_annee_construction(?int $min = null, ?int $max = null): static
    {
        $min ? $this->searcher->addQuery('annee_construction_gte', $min) : null;
        $max ? $this->searcher->addQuery('annee_construction_lte', $max) : null;
        return $this;
    }

    public function with_altitude(?float $min = null, ?float $max = null): static
    {
        $min ? $this->searcher->addQuery('altitude_gte', $min) : null;
        $max ? $this->searcher->addQuery('altitude_lte', $max) : null;
        return $this;
    }

    public function with_type_batiment(TypeBatiment ...$filters): static
    {
        $filters ? $this->searcher->addQuery('type_batiment_in', array_column($filters, 'value')) : null;
        return $this;
    }

    public function with_etiquette_energie(Etiquette ...$filters): static
    {
        $filters ? $this->searcher->addQuery('etiquette_dpe_in', array_column($filters, 'value')) : null;
        return $this;
    }

    public function with_etiquette_climat(Etiquette ...$filters): static
    {
        $filters ? $this->searcher->addQuery('etiquette_ges_in', array_column($filters, 'value')) : null;
        return $this;
    }

    public function with_code_postal(string ...$filters): static
    {
        $filters ? $this->searcher->addQuery('ban_code_postal_in', $filters) : null;
        return $this;
    }

    public function with_code_departement(string ...$filters): static
    {
        $filters ? $this->searcher->addQuery('ban_code_departement_in', $filters) : null;
        return $this;
    }

    public function with_zone_climatique(ZoneClimatique ...$filters): static
    {
        $filters ? $this->searcher->addQuery('zone_climatique_in', array_column($filters, 'value')) : null;
        return $this;
    }
}
