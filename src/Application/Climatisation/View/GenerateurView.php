<?php

namespace App\Application\Climatisation\View;

use App\Domain\Climatisation\Engine\{GenerateurEngine, GenerateurEngineCollection};
use App\Domain\Climatisation\Entity\{Generateur, GenerateurCollection};
use App\Domain\Common\Enum\{Enum, Mois};
use App\Domain\Common\Table\TableValue;

class GenerateurView
{
    public function __construct(
        public readonly string $id,
        public readonly string $description,
        public readonly float $surface,
        public readonly Enum $type_generateur,
        public readonly int $annee_installation,
        public readonly ?Enum $energie,
        public readonly ?float $seer_saisi,
        public readonly ?float $cfr = null,
        public readonly ?float $cfr_depensier = null,
        /** @var ?array<float> */
        public readonly ?array $cfr_j = null,
        /** @var ?array<float> */
        public readonly ?array $cfr_j_depensier = null,
        public readonly ?float $eer = null,
        public readonly ?float $rdim = null,
        public readonly ?TableValue $table_seer = null,
    ) {
    }

    public static function from_entity(Generateur $entity): self
    {
        return new self(
            id: $entity->id(),
            description: $entity->description(),
            surface: $entity->surface()->valeur(),
            type_generateur: $entity->type_generateur(),
            annee_installation: $entity->annee_installation()->valeur(),
            energie: $entity->energie(),
            seer_saisi: $entity->seer()?->valeur(),
        );
    }

    /** @return self[] */
    public static function from_entity_collection(GenerateurCollection $collection): array
    {
        return \array_map(fn (Generateur $entity) => self::from_entity($entity), $collection->to_array());
    }

    public static function from_engine(GenerateurEngine $engine): self
    {
        $entity = $engine->input();
        return new self(
            id: $entity->id(),
            description: $entity->description(),
            surface: $entity->surface()->valeur(),
            type_generateur: $entity->type_generateur(),
            annee_installation: $entity->annee_installation()->valeur(),
            energie: $entity->energie(),
            seer_saisi: $entity->seer()?->valeur(),
            cfr: $engine->cfr(),
            cfr_depensier: $engine->cfr(true),
            cfr_j: \array_map(fn (Mois $mois): float => $engine->cfr_j($mois), Mois::cases()),
            cfr_j_depensier: \array_map(fn (Mois $mois): float => $engine->cfr_j($mois, true), Mois::cases()),
            eer: $engine->eer(),
            rdim: $engine->rdim(),
            table_seer: $engine->table_seer(),
        );
    }

    /** @return self[] */
    public static function from_engine_collection(GenerateurEngineCollection $collection): array
    {
        return \array_map(fn (GenerateurEngine $engine) => self::from_engine($engine), $collection->liste());
    }
}
