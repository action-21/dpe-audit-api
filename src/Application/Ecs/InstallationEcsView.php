<?php

namespace App\Application\Ecs;

use App\Application\Ecs\View\GenerateurView;
use App\Domain\Common\Enum\{Enum, Mois};
use App\Domain\Ecs\{InstallationEcs, InstallationEcsCollection, InstallationEcsEngine, InstallationEcsEngineCollection};

class InstallationEcsView
{
    public function __construct(
        public readonly string $id,
        public readonly string $description,
        public readonly ?bool $reseau_distribution_isole,
        public readonly ?bool $pieces_contigues,
        public readonly int $niveaux_desservis,
        public readonly Enum $type_installation,
        public readonly Enum $bouclage_reseau,
        /** @var array<GenerateurView> */
        public readonly array $generateur_collection,
        public readonly ?Enum $type_installation_solaire,
        public readonly ?float $fecs_saisi,
        public readonly ?float $cecs = null,
        public readonly ?float $cecs_depensier = null,
        /** @var ?array<float> */
        public readonly ?array $cecs_j = null,
        /** @var ?array<float> */
        public readonly ?array $cecs_j_depensier = null,
        public readonly ?float $rdim = null,
        public readonly ?float $fecs = null,
    ) {
    }

    public static function from_entity(InstallationEcs $entity): self
    {
        return new self(
            id: $entity->id(),
            description: $entity->description(),
            reseau_distribution_isole: $entity->reseau_distribution_isole(),
            pieces_contigues: $entity->pieces_contigues(),
            niveaux_desservis: $entity->niveaux_desservis()->valeur(),
            type_installation: $entity->type_installation(),
            bouclage_reseau: $entity->bouclage_reseau(),
            type_installation_solaire: $entity->type_installation_solaire(),
            fecs_saisi: $entity->fecs()?->valeur(),
            generateur_collection: GenerateurView::from_entity_collection($entity->generateur_collection()),
        );
    }

    /** @return self[] */
    public static function from_entity_collection(InstallationEcsCollection $collection): array
    {
        return \array_map(fn (InstallationEcs $entity) => self::from_entity($entity), $collection->to_array());
    }

    public static function from_engine(InstallationEcsEngine $engine): self
    {
        $entity = $engine->input();
        return new self(
            id: $entity->id(),
            description: $entity->description(),
            reseau_distribution_isole: $entity->reseau_distribution_isole(),
            pieces_contigues: $entity->pieces_contigues(),
            niveaux_desservis: $entity->niveaux_desservis()->valeur(),
            type_installation: $entity->type_installation(),
            bouclage_reseau: $entity->bouclage_reseau(),
            type_installation_solaire: $entity->type_installation_solaire(),
            fecs_saisi: $entity->fecs()?->valeur(),
            generateur_collection: GenerateurView::from_engine_collection($engine->generateur_engine_collection()),
            cecs: $engine->cecs(),
            cecs_depensier: $engine->cecs(scenario_depensier: true),
            cecs_j: \array_map(fn (Mois $mois): float => $engine->cecs_j($mois), Mois::cases()),
            cecs_j_depensier: \array_map(fn (Mois $mois): float => $engine->cecs_j(mois: $mois, scenario_depensier: true), Mois::cases()),
            rdim: $engine->rdim(),
            fecs: $engine->fecs(),
        );
    }

    /** @return self[] */
    public static function from_engine_collection(InstallationEcsEngineCollection $collection): array
    {
        return \array_map(fn (InstallationEcsEngine $engine) => self::from_engine($engine), $collection->liste());
    }
}
