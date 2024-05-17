<?php

namespace App\Application\MasqueLointain;

use App\Domain\Common\Enum\Enum;
use App\Domain\Common\Table\TableValue;
use App\Domain\MasqueLointain\{MasqueLointain, MasqueLointainCollection, MasqueLointainEngine, MasqueLointainEngineCollection};

class MasqueLointainView
{
    public function __construct(
        public readonly string $id,
        public readonly string $description,
        private Enum $type_masque_lointain,
        private float $hauteur_alpha,
        private float $orientation,
        private ?Enum $secteur_orientation,
        public readonly null|float $fe2 = null,
        public readonly null|float $omb = null,
        public readonly ?TableValue $table_fe2 = null,
        public readonly ?TableValue $table_omb = null,
    ) {
    }

    public static function from_entity(MasqueLointain $entity): self
    {
        return new self(
            id: $entity->id(),
            description: $entity->description(),
            type_masque_lointain: $entity->type_masque(),
            hauteur_alpha: $entity->hauteur_alpha()->valeur(),
            orientation: $entity->orientation()->valeur(),
            secteur_orientation: $entity->secteur_orientation(),
        );
    }

    /** @return self[] */
    public static function from_entity_collection(MasqueLointainCollection $collection): array
    {
        return \array_map(fn (MasqueLointain $entity) => self::from_entity($entity), $collection->to_array());
    }

    public static function from_engine(MasqueLointainEngine $engine): self
    {
        $entity = $engine->input();
        return new self(
            id: $entity->id(),
            description: $entity->description(),
            type_masque_lointain: $entity->type_masque(),
            hauteur_alpha: $entity->hauteur_alpha()->valeur(),
            orientation: $entity->orientation()->valeur(),
            secteur_orientation: $entity->secteur_orientation(),
            fe2: $engine->fe2(),
            omb: $engine->omb(),
            table_fe2: $engine->table_fe2(),
            table_omb: $engine->table_omb(),
        );
    }

    /** @return self[] */
    public static function from_engine_collection(MasqueLointainEngineCollection $collection): array
    {
        return \array_map(fn (MasqueLointainEngine $engine) => self::from_engine($engine), $collection->liste());
    }
}
