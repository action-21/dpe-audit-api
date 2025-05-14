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

        public ?string $sort = null,

        public ?\DateTimeInterface $date_etablissement_min = null,
        public ?\DateTimeInterface $date_etablissement_max = null,

        public ?float $surface_habitable_min = null,
        public ?float $surface_habitable_max = null,

        public ?int $annee_construction_min = null,
        public ?int $annee_construction_max = null,

        public ?int $altitude_min = null,
        public ?int $altitude_max = null,

        public readonly array $type_batiment = [],
        public readonly array $etiquette_energie = [],
        public readonly array $etiquette_climat = [],
        public readonly array $code_postal = [],
        public readonly array $code_departement = [],
        public readonly array $zone_climatique = [],
    ) {}

    public function with_periode_construction(PeriodeConstruction $enum): self
    {
        if ($enum === PeriodeConstruction::AVANT_1948) {
            $this->annee_construction_min = null;
            $this->annee_construction_max = 1948;
        }
        if ($enum === PeriodeConstruction::ENTRE_1948_1974) {
            $this->annee_construction_min = 1948;
            $this->annee_construction_max = 1974;
        }
        if ($enum === PeriodeConstruction::ENTRE_1975_1977) {
            $this->annee_construction_min = 1975;
            $this->annee_construction_max = 1977;
        }
        if ($enum === PeriodeConstruction::ENTRE_1978_1982) {
            $this->annee_construction_min = 1978;
            $this->annee_construction_max = 1982;
        }
        if ($enum === PeriodeConstruction::ENTRE_1983_1988) {
            $this->annee_construction_min = 1983;
            $this->annee_construction_max = 1988;
        }
        if ($enum === PeriodeConstruction::ENTRE_1989_2000) {
            $this->annee_construction_min = 1989;
            $this->annee_construction_max = 2000;
        }
        if ($enum === PeriodeConstruction::ENTRE_2001_2005) {
            $this->annee_construction_min = 2001;
            $this->annee_construction_max = 2005;
        }
        if ($enum === PeriodeConstruction::ENTRE_2006_2012) {
            $this->annee_construction_min = 2006;
            $this->annee_construction_max = 2012;
        }
        if ($enum === PeriodeConstruction::ENTRE_2013_2021) {
            $this->annee_construction_min = 2013;
            $this->annee_construction_max = 2021;
        }
        if ($enum === PeriodeConstruction::APRES_2021) {
            $this->annee_construction_min = 2021;
            $this->annee_construction_max = null;
        }
        return $this;
    }

    public function with_classe_altitude(ClasseAltitude $enum): self
    {
        if ($enum === ClasseAltitude::_400_LT) {
            $this->altitude_min = null;
            $this->altitude_max = 399;
        }
        if ($enum === ClasseAltitude::_400_800) {
            $this->altitude_min = 400;
            $this->altitude_max = 800;
        }
        if ($enum === ClasseAltitude::_800_GT) {
            $this->altitude_min = 801;
            $this->altitude_max = null;
        }
        return $this;
    }
}
