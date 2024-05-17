<?php

namespace App\Application\Mur;

use App\Domain\Common\Enum\Enum;
use App\Domain\Common\Table\TableValue;
use App\Domain\Mur\{Mur, MurCollection, MurEngine, MurEngineCollection};

class MurView
{
    public function __construct(
        public readonly string $id,
        public readonly ?string $reference_local_non_chauffe,
        public readonly string $description,
        public readonly Enum $mitoyennete,
        public readonly Enum $materiaux_structure,
        public readonly Enum $inertie,
        public readonly Enum $type_doublage,
        public readonly float $surface,
        public readonly ?float $epaisseur,
        public readonly bool $enduit_isolant,
        public readonly bool $paroi_ancienne,
        public readonly ?float $umur0_saisi,
        public readonly ?float $umur_saisi,
        public readonly Enum $type_isolation,
        public readonly ?int $annnee_isolation,
        public readonly ?float $epaisseur_isolant,
        public readonly ?float $resistance_thermique,
        public readonly float $orientation,
        public readonly ?float $dp = null,
        public readonly ?float $umur0 = null,
        public readonly ?float $umur = null,
        public readonly ?float $b = null,
        public readonly ?float $sdep = null,
        public readonly ?Enum $qualite_isolation = null,
        public readonly ?TableValue $table_b = null,
        public readonly ?TableValue $table_umur = null,
        /** @var ?array<TableValue> */
        public readonly ?array $table_umur0_collection = null,
    ) {
    }

    public static function from_entity(Mur $entity): self
    {
        return new self(
            id: $entity->id(),
            reference_local_non_chauffe: $entity->local_non_chauffe()?->id(),
            description: $entity->description(),
            mitoyennete: $entity->mitoyennete(),
            materiaux_structure: $entity->caracteristique()->materiaux_structure,
            type_doublage: $entity->caracteristique()->type_doublage,
            inertie: $entity->caracteristique()->inertie,
            surface: $entity->caracteristique()->surface->valeur(),
            epaisseur: $entity->caracteristique()->epaisseur_structure?->valeur(),
            enduit_isolant: $entity->caracteristique()->enduit_isolant,
            paroi_ancienne: $entity->caracteristique()->paroi_ancienne,
            umur0_saisi: $entity->caracteristique()->umur0?->valeur(),
            umur_saisi: $entity->caracteristique()->umur?->valeur(),
            type_isolation: $entity->isolation()->type_isolation,
            annnee_isolation: $entity->isolation()->annnee_isolation?->valeur(),
            epaisseur_isolant: $entity->isolation()->epaisseur_isolant?->valeur(),
            resistance_thermique: $entity->isolation()->resistance_thermique?->valeur(),
            orientation: $entity->orientation()->valeur(),
        );
    }

    /** @return self[] */
    public static function from_entity_collection(MurCollection $collection): array
    {
        return \array_map(fn (Mur $entity) => self::from_entity($entity), $collection->to_array());
    }

    public static function from_engine(MurEngine $engine): self
    {
        $entity = $engine->input();
        return new self(
            id: $entity->id(),
            reference_local_non_chauffe: $entity->local_non_chauffe()?->id(),
            description: $entity->description(),
            mitoyennete: $entity->mitoyennete(),
            materiaux_structure: $entity->caracteristique()->materiaux_structure,
            type_doublage: $entity->caracteristique()->type_doublage,
            inertie: $entity->caracteristique()->inertie,
            surface: $entity->caracteristique()->surface->valeur(),
            epaisseur: $entity->caracteristique()->epaisseur_structure?->valeur(),
            enduit_isolant: $entity->caracteristique()->enduit_isolant,
            paroi_ancienne: $entity->caracteristique()->paroi_ancienne,
            umur0_saisi: $entity->caracteristique()->umur0?->valeur(),
            umur_saisi: $entity->caracteristique()->umur?->valeur(),
            type_isolation: $entity->isolation()->type_isolation,
            annnee_isolation: $entity->isolation()->annnee_isolation?->valeur(),
            epaisseur_isolant: $entity->isolation()->epaisseur_isolant?->valeur(),
            resistance_thermique: $entity->isolation()->resistance_thermique?->valeur(),
            orientation: $entity->orientation()->valeur(),
            dp: $engine->dp(),
            umur0: $engine->u0(),
            umur: $engine->u(),
            b: $engine->b(),
            sdep: $engine->sdep(),
            qualite_isolation: $engine->qualite_isolation(),
            table_b: $engine->table_b(),
            table_umur0_collection: $engine->table_umur0_collection()->to_array(),
            table_umur: $engine->table_umur(),
        );
    }

    /** @return self[] */
    public static function from_engine_collection(MurEngineCollection $collection): array
    {
        return \array_map(fn (MurEngine $engine) => self::from_engine($engine), $collection->liste());
    }
}
