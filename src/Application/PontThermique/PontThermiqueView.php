<?php

namespace App\Application\PontThermique;

use App\Domain\Common\Enum\Enum;
use App\Domain\Common\Table\TableValue;
use App\Domain\PontThermique\{PontThermique, PontThermiqueCollection};
use App\Domain\PontThermique\{PontThermiqueEngine, PontThermiqueEngineCollection};

class PontThermiqueView
{
    public function __construct(
        public readonly string $id,
        public readonly ?string $reference_mur,
        public readonly ?string $reference_plancher,
        public readonly ?string $reference_refend,
        public readonly ?string $reference_ouverture,
        public readonly Enum $type_liaison,
        public readonly string $description,
        public readonly float $longueur,
        public readonly ?float $kpt_saisi,
        public readonly bool $pont_thermique_partiel,
        public readonly null|float $pt = null,
        public readonly null|float $l = null,
        public readonly null|float $k = null,
        public readonly null|float $coefficient_partiel = null,
        public readonly ?TableValue $table_kpt = null,
    ) {
    }

    public static function from_entity(PontThermique $entity): self
    {
        return new self(
            id: $entity->id(),
            reference_mur: $entity->mur()?->id(),
            reference_plancher: $entity->plancher()?->id(),
            reference_refend: $entity->refend()?->id(),
            reference_ouverture: $entity->ouverture()?->id(),
            type_liaison: $entity->type_liaison(),
            description: $entity->description(),
            longueur: $entity->longueur()->valeur(),
            kpt_saisi: $entity->valeur()?->valeur(),
            pont_thermique_partiel: $entity->pont_thermique_partiel(),
        );
    }

    /** @return self[] */
    public static function from_entity_collection(PontThermiqueCollection $collection): array
    {
        return \array_map(fn (PontThermique $entity) => self::from_entity($entity), $collection->to_array());
    }

    public static function from_engine(PontThermiqueEngine $engine): self
    {
        $entity = $engine->input();
        return new self(
            id: $entity->id(),
            reference_mur: $entity->mur()?->id(),
            reference_plancher: $entity->plancher()?->id(),
            reference_refend: $entity->refend()?->id(),
            reference_ouverture: $entity->ouverture()?->id(),
            type_liaison: $entity->type_liaison(),
            description: $entity->description(),
            longueur: $entity->longueur()->valeur(),
            kpt_saisi: $entity->valeur()?->valeur(),
            pont_thermique_partiel: $entity->pont_thermique_partiel(),
            pt: $engine->pt(),
            l: $engine->l(),
            k: $engine->k(),
            coefficient_partiel: $engine->coefficient_partiel(),
            table_kpt: $engine->table_k(),
        );
    }

    /** @return self[] */
    public static function from_engine_collection(PontThermiqueEngineCollection $collection): array
    {
        return \array_map(fn (PontThermiqueEngine $engine) => self::from_engine($engine), $collection->liste());
    }
}
