<?php

namespace App\Application\Logement;

use App\Application\Logement\View\EtageView;
use App\Domain\Common\Enum\Enum;
use App\Domain\Logement\{Logement, LogementCollection, LogementEngine, LogementEngineCollection};

class LogementView
{
    public function __construct(
        public readonly \Stringable $id,
        public string $description,
        /** @var array<EtageView> */
        public array $etage_collection,
        public ?Enum $position,
        public ?Enum $typologie,
    ) {
    }

    public static function from_entity(Logement $entity): self
    {
        return new self(
            id: $entity->id(),
            description: $entity->description(),
            etage_collection: EtageView::from_entity_collection($entity->etage_collection()),
            position: $entity->position(),
            typologie: $entity->typologie(),
        );
    }

    /** @return self[] */
    public static function from_entity_collection(LogementCollection $collection): array
    {
        return \array_map(fn (Logement $entity) => self::from_entity($entity), $collection->to_array());
    }

    public static function from_engine(LogementEngine $engine): self
    {
        $entity = $engine->input();
        return new self(
            id: $entity->id(),
            description: $entity->description(),
            etage_collection: EtageView::from_entity_collection($entity->etage_collection()),
            position: $entity->position(),
            typologie: $entity->typologie(),
        );
    }

    /** @return self[] */
    public static function from_engine_collection(LogementEngineCollection $collection): array
    {
        return \array_map(fn (LogementEngine $engine) => self::from_engine($engine), $collection->liste());
    }
}
