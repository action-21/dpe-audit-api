<?php

namespace App\Application\Lnc\View;

use App\Domain\Common\Enum\{Enum, Mois};
use App\Domain\Common\Table\TableValue;
use App\Domain\Lnc\Engine\{BaieEngine, BaieEngineCollection};
use App\Domain\Lnc\Entity\{Baie, BaieCollection};

class BaieView
{
    public function __construct(
        public readonly string $id,
        public readonly string $description,
        public readonly float $surface,
        public readonly bool $isolation,
        public readonly float $inclinaison_vitrage,
        public readonly Enum $nature_menuiserie,
        public readonly ?Enum $type_vitrage,
        public readonly ?float $orientation,
        /** @var ?array<float> */
        public readonly ?array $sst_j = null,
        /** @var ?array<float> */
        public readonly ?array $c1_j = null,
        public readonly ?float $fe = null,
        public readonly ?float $t = null,
        public readonly ?TableValue $table_t = null,
    ) {
    }

    public static function from_entity(Baie $entity): self
    {
        return new self(
            id: $entity->id(),
            description: $entity->description(),
            surface: $entity->surface()->valeur(),
            isolation: $entity->type_vitrage()->est_isole(),
            inclinaison_vitrage: $entity->inclinaison_vitrage()->valeur(),
            nature_menuiserie: $entity->nature_menuiserie(),
            orientation: $entity->orientation()?->valeur(),
            type_vitrage: $entity->type_vitrage(),
        );
    }

    /** @return self[] */
    public static function from_entity_collection(BaieCollection $collection): array
    {
        return \array_map(fn (Baie $entity) => self::from_entity($entity), $collection->to_array());
    }

    public static function from_engine(BaieEngine $engine): self
    {
        $entity = $engine->input();
        return new self(
            id: $entity->id(),
            description: $entity->description(),
            surface: $entity->surface()->valeur(),
            isolation: $entity->type_vitrage()->est_isole(),
            inclinaison_vitrage: $entity->inclinaison_vitrage()->valeur(),
            nature_menuiserie: $entity->nature_menuiserie(),
            orientation: $entity->orientation()?->valeur(),
            type_vitrage: $entity->type_vitrage(),
            sst_j: \array_map(fn (Mois $mois) => $engine->sst_j($mois), Mois::cases()),
            c1_j: \array_map(fn (Mois $mois) => $engine->c1_j($mois), Mois::cases()),
            fe: $engine->fe(),
            t: $engine->t(),
            table_t: $engine->table_t(),
        );
    }

    /** @return self[] */
    public static function from_engine_collection(BaieEngineCollection $collection): array
    {
        return \array_map(fn (BaieEngine $engine) => self::from_engine($engine), $collection->liste());
    }
}
