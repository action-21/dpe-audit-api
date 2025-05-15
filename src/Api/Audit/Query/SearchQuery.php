<?php

namespace App\Api\Audit\Query;

use App\Domain\Audit\Enum\{ClasseAltitude, Etiquette, PeriodeConstruction, TypeBatiment};
use App\Domain\Common\Enum\ZoneClimatique;

/**
 * @property TypeBatiment[] $type_batiment
 * @property Etiquette[] $etiquette_energie
 * @property Etiquette[] $etiquette_climat
 * @property PeriodeConstruction[] $periode_constrcution
 * @property ClasseAltitude[] $classe_altitude
 * @property string[] $code_postal
 * @property string[] $code_departement
 * @property ZoneClimatique[] $zone_climatique
 */
final class SearchQuery
{
    public function __construct(
        public int $page = 1,
        public int $itemsPerPage = 100,
        public string $sort = '-date_etablissement_dpe',
        public bool $randomize = false,

        public ?\DateTimeInterface $date_etablissement_min = null,
        public ?\DateTimeInterface $date_etablissement_max = null,

        public ?float $surface_habitable_min = null,
        public ?float $surface_habitable_max = null,

        public ?int $annee_construction_min = null,
        public ?int $annee_construction_max = null,

        public ?int $altitude_min = null,
        public ?int $altitude_max = null,

        public array $type_batiment = [],
        public array $periode_constrcution = [],
        public array $classe_altitude = [],
        public array $etiquette_energie = [],
        public array $etiquette_climat = [],
        public array $code_postal = [],
        public array $code_departement = [],
        public array $zone_climatique = [],
    ) {}
}
