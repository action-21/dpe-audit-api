<?php

namespace App\Application\Lnc;

use App\Application\Lnc\View\{BaieView, ParoiOpaqueView};
use App\Domain\Common\Enum\{Enum, Mois};
use App\Domain\Common\Table\TableValue;
use App\Domain\Lnc\{Lnc, LncCollection, LncEngine, LncEngineCollection};

class LncView
{
    public function __construct(
        public readonly string $id,
        public readonly string $description,
        public readonly Enum $type_lnc,
        /** @array<BaieView> */
        public readonly array $baie_collection,
        /** @array<ParoiOpaqueView> */
        public readonly array $paroi_opaque_collection,
        /** @var ?array<float> */
        public readonly ?array $sst_j = null,
        public readonly ?float $b = null,
        public readonly ?float $bver = null,
        public readonly ?float $t = null,
        public readonly ?float $uvue = null,
        public readonly ?TableValue $table_uvue = null,
        public readonly ?TableValue $table_b = null,
        /** @var ?array<TableValue> */
        public readonly ?array $table_bver = null,
    ) {
    }

    public static function from_entity(Lnc $entity): self
    {
        return new self(
            id: $entity->id(),
            description: $entity->description(),
            type_lnc: $entity->type_lnc(),
            baie_collection: BaieView::from_entity_collection($entity->baie_collection()),
            paroi_opaque_collection: ParoiOpaqueView::from_entity_collection($entity->paroi_opaque_collection()),
        );
    }

    /** @return self[] */
    public static function from_entity_collection(LncCollection $collection): array
    {
        return \array_map(fn (Lnc $entity) => self::from_entity($entity), $collection->to_array());
    }

    public static function from_engine(LncEngine $engine): self
    {
        $entity = $engine->input();
        return new self(
            id: $entity->id(),
            description: $entity->description(),
            type_lnc: $entity->type_lnc(),
            baie_collection: BaieView::from_engine_collection($engine->baie_engine_collection()),
            paroi_opaque_collection: ParoiOpaqueView::from_entity_collection($entity->paroi_opaque_collection()),
            sst_j: \array_map(fn (Mois $mois) => $engine->sst_j($mois), Mois::cases()),
            b: $engine->b(),
            bver: $engine->bver(),
            t: $engine->t(),
            uvue: $engine->uvue(),
            table_uvue: $engine->table_uvue(),
            table_b: $engine->table_b(),
            table_bver: $engine->table_bver_collection()->to_array(),
        );
    }

    /** @return self[] */
    public static function from_engine_collection(LncEngineCollection $collection): array
    {
        return \array_map(fn (LncEngine $engine) => self::from_engine($engine), $collection->liste());
    }
}
