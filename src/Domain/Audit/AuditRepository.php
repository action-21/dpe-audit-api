<?php

namespace App\Domain\Audit;

use App\Domain\Audit\Enum\{ClasseAltitude, Etiquette, PeriodeConstruction, TypeBatiment};
use App\Domain\Common\Enum\ZoneClimatique;
use App\Domain\Common\ValueObject\Id;

interface AuditRepository
{
    public function find(Id $id): ?Audit;

    public function search(int $page = 1): AuditCollection;

    public function count(): int;

    public function sort(string $name): static;

    public function randomize(): static;

    public function with_type_batiment(TypeBatiment ...$filters): static;

    public function with_etiquette_energie(Etiquette ...$filters): static;

    public function with_etiquette_climat(Etiquette ...$filters): static;

    public function with_date_etablissement(?\DateTimeInterface $min = null, ?\DateTimeInterface $max = null): static;

    public function with_surface_habitable(?float $min = null, ?float $max = null): static;

    public function with_annee_construction(?int $min = null, ?int $max = null): static;

    public function with_altitude(?float $min = null, ?float $max = null): static;

    public function with_code_postal(string ...$filters): static;

    public function with_code_departement(string ...$filters): static;

    public function with_zone_climatique(ZoneClimatique ...$filters): static;
}
