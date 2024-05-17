<?php

namespace App\Application\MasqueProche;

use App\Domain\Common\Enum\Enum;
use App\Domain\Common\Table\TableValue;
use App\Domain\MasqueProche\{MasqueProche, MasqueProcheCollection, MasqueProcheEngine, MasqueProcheEngineCollection};

class MasqueProcheView
{
    public function __construct(
        public readonly string $id,
        public readonly string $description,
        public readonly Enum $type_masque_proche,
        public readonly ?float $avancee,
        public readonly ?float $orientation,
        public readonly null|float $fe1 = null,
        public readonly ?TableValue $table_fe1 = null,
    ) {
    }

    public static function from_entity(MasqueProche $entity): self
    {
        return new self(
            id: $entity->id(),
            description: $entity->description(),
            type_masque_proche: $entity->type_masque_proche(),
            avancee: $entity->avancee()?->valeur(),
            orientation: $entity->orientation()?->valeur(),
        );
    }

    /** @return self[] */
    public static function from_entity_collection(MasqueProcheCollection $collection): array
    {
        return \array_map(fn (MasqueProche $entity) => self::from_entity($entity), $collection->to_array());
    }

    public static function from_engine(MasqueProcheEngine $engine): self
    {
        $entity = $engine->input();
        return new self(
            id: $entity->id(),
            description: $entity->description(),
            type_masque_proche: $entity->type_masque_proche(),
            avancee: $entity->avancee()?->valeur(),
            orientation: $entity->orientation()?->valeur(),
            fe1: $engine->fe1(),
            table_fe1: $engine->table_fe1(),
        );
    }

    /** @return self[] */
    public static function from_engine_collection(MasqueProcheEngineCollection $collection): array
    {
        return \array_map(fn (MasqueProcheEngine $engine) => self::from_engine($engine), $collection->liste());
    }
}
