<?php

namespace App\Application\Photovoltaique\View;

use App\Domain\Common\Enum\Mois;
use App\Domain\Common\Table\TableValue;
use App\Domain\Photovoltaique\Engine\{PanneauPhotovoltaiqueEngine, PanneauPhotovoltaiqueEngineCollection};
use App\Domain\Photovoltaique\Entity\{PanneauPhotovoltaique, PanneauPhotovoltaiqueCollection};

class PanneauPhotovoltaiqueView
{
    public function __construct(
        public readonly string $id,
        public readonly float $surface_capteurs,
        public readonly ?int $modules,
        public readonly ?float $orientation,
        public readonly ?float $inclinaison,
        /** @var ?array<float> */
        public readonly ?array $ppv_j = null,
        public readonly ?float $ppv = null,
        public readonly ?float $rendement = null,
        public readonly ?float $coefficient_perte = null,
        public readonly ?float $scapteurs = null,
        public readonly ?float $k = null,
        public readonly ?TableValue $table_k = null,
    ) {
    }

    public static function from_entity(PanneauPhotovoltaique $entity): self
    {
        return new self(
            id: $entity->id(),
            surface_capteurs: $entity->surface_capteurs()->valeur(),
            modules: $entity->modules()?->valeur(),
            orientation: $entity->orientation()?->valeur(),
            inclinaison: $entity->inclinaison()?->valeur(),
        );
    }

    /** @return self[] */
    public static function from_entity_collection(PanneauPhotovoltaiqueCollection $collection): array
    {
        return \array_map(fn (PanneauPhotovoltaique $entity) => self::from_entity($entity), $collection->to_array());
    }

    public static function from_engine(PanneauPhotovoltaiqueEngine $engine): self
    {
        $entity = $engine->input();
        return new self(
            id: $entity->id(),
            surface_capteurs: $entity->surface_capteurs()->valeur(),
            modules: $entity->modules()?->valeur(),
            orientation: $entity->orientation()?->valeur(),
            inclinaison: $entity->inclinaison()?->valeur(),
            ppv: $engine->ppv(),
            rendement: $engine::RENDEMENT,
            coefficient_perte: $engine::COEFFICIENT_PERTE,
            scapteurs: $engine->scapteurs(),
            k: $engine->k(),
            table_k: $engine->table_k(),
            ppv_j: \array_map(fn (Mois $mois) => $engine->ppv_j($mois), Mois::cases()),
        );
    }

    /** @return self[] */
    public static function from_engine_collection(PanneauPhotovoltaiqueEngineCollection $collection): array
    {
        return \array_map(fn (PanneauPhotovoltaiqueEngine $engine) => self::from_engine($engine), $collection->liste());
    }
}
