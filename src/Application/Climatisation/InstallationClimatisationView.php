<?php

namespace App\Application\Climatisation;

use App\Application\Climatisation\View\GenerateurView;
use App\Domain\Climatisation\{InstallationClimatisation, InstallationClimatisationCollection};
use App\Domain\Climatisation\{InstallationClimatisationEngine, InstallationClimatisationEngineCollection};

final class InstallationClimatisationView
{
    public function __construct(
        public readonly string $logement_id,
        /** @var array<GenerateurView> */
        public readonly array $generateur_collection,
        public readonly ?float $cfr = null,
        public readonly ?float $cfr_depensier = null,
        /** @var ?array<float> */
        public readonly ?array $cfr_j = null,
        /** @var ?array<float> */
        public readonly ?array $cfr_j_depensier = null,
        public readonly ?float $eer = null,
        public readonly ?float $rdim = null,
    ) {
    }

    public static function from_entity(InstallationClimatisation $entity): self
    {
        return new self(
            logement_id: $entity->logement()->id(),
            generateur_collection: GenerateurView::from_entity_collection($entity->generateur_collection()),
        );
    }

    /** @return self[] */
    public static function from_entity_collection(InstallationClimatisationCollection $collection): array
    {
        return \array_map(fn (InstallationClimatisation $entity) => self::from_entity($entity), $collection->to_array());
    }

    public static function from_engine(InstallationClimatisationEngine $engine): self
    {
        $entity = $engine->input();
        return new self(
            logement_id: $entity->logement()->id(),
            generateur_collection: GenerateurView::from_engine_collection($engine->generateur_engine_collection()),
        );
    }

    /** @return self[] */
    public static function from_engine_collection(InstallationClimatisationEngineCollection $collection): array
    {
        return \array_map(fn (InstallationClimatisationEngine $engine) => self::from_engine($engine), $collection->liste());
    }
}
