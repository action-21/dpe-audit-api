<?php

namespace App\Application\PlancherHaut;

use App\Domain\Common\Enum\Enum;
use App\Domain\Common\Table\TableValue;
use App\Domain\PlancherHaut\{PlancherHaut, PlancherHautCollection};
use App\Domain\PlancherHaut\{PlancherHautEngine, PlancherHautEngineCollection};

class PlancherHautView
{
    public function __construct(
        public readonly string $id,
        public readonly ?string $reference_local_non_chauffe,
        public readonly string $description,
        public readonly Enum $mitoyennete,
        public readonly Enum $configuration,
        public readonly Enum $type_plancher_haut,
        public readonly Enum $inertie,
        public readonly float $surface,
        public readonly ?float $uph0_saisi,
        public readonly ?float $uph_saisi,
        public readonly Enum $type_isolation,
        public readonly ?int $annnee_isolation,
        public readonly ?float $epaisseur_isolant,
        public readonly ?float $resistance_thermique,
        public readonly ?float $orientation,
        public readonly ?float $dp = null,
        public readonly ?float $uph0 = null,
        public readonly ?float $uph = null,
        public readonly ?float $b = null,
        public readonly ?float $sdep = null,
        public readonly ?Enum $qualite_isolation = null,
        public readonly ?TableValue $table_b = null,
        public readonly ?TableValue $table_uph0 = null,
        public readonly ?TableValue $table_uph = null,
    ) {
    }

    public static function from_entity(PlancherHaut $entity): self
    {
        return new self(
            id: $entity->id(),
            reference_local_non_chauffe: $entity->local_non_chauffe()?->id(),
            description: $entity->description(),
            mitoyennete: $entity->mitoyennete(),
            configuration: $entity->configuration(),
            type_plancher_haut: $entity->caracteristique()->type_plancher_haut,
            inertie: $entity->caracteristique()->inertie,
            surface: $entity->caracteristique()->surface->valeur(),
            uph0_saisi: $entity->caracteristique()->uph0?->valeur(),
            uph_saisi: $entity->caracteristique()->uph?->valeur(),
            type_isolation: $entity->isolation()->type_isolation,
            annnee_isolation: $entity->isolation()->annnee_isolation?->valeur(),
            epaisseur_isolant: $entity->isolation()->epaisseur_isolant?->valeur(),
            resistance_thermique: $entity->isolation()->resistance_thermique?->valeur(),
            orientation: $entity->orientation()?->valeur(),
        );
    }

    /** @return self[] */
    public static function from_entity_collection(PlancherHautCollection $collection): array
    {
        return \array_map(fn (PlancherHaut $entity) => self::from_entity($entity), $collection->to_array());
    }

    public static function from_engine(PlancherHautEngine $engine): self
    {
        $entity = $engine->input();
        return new self(
            id: $entity->id(),
            reference_local_non_chauffe: $entity->local_non_chauffe()?->id(),
            description: $entity->description(),
            mitoyennete: $entity->mitoyennete(),
            configuration: $entity->configuration(),
            type_plancher_haut: $entity->caracteristique()->type_plancher_haut,
            inertie: $entity->caracteristique()->inertie,
            surface: $entity->caracteristique()->surface->valeur(),
            uph0_saisi: $entity->caracteristique()->uph0?->valeur(),
            uph_saisi: $entity->caracteristique()->uph?->valeur(),
            type_isolation: $entity->isolation()->type_isolation,
            annnee_isolation: $entity->isolation()->annnee_isolation?->valeur(),
            epaisseur_isolant: $entity->isolation()->epaisseur_isolant?->valeur(),
            resistance_thermique: $entity->isolation()->resistance_thermique?->valeur(),
            orientation: $entity->orientation()?->valeur(),
            dp: $engine->dp(),
            uph0: $engine->u0(),
            uph: $engine->u(),
            b: $engine->b(),
            sdep: $engine->sdep(),
            qualite_isolation: $engine->qualite_isolation(),
            table_b: $engine->table_b(),
            table_uph0: $engine->table_uph0(),
            table_uph: $engine->table_uph(),
        );
    }

    /** @return self[] */
    public static function from_engine_collection(PlancherHautEngineCollection $collection): array
    {
        return \array_map(fn (PlancherHautEngine $engine) => self::from_engine($engine), $collection->liste());
    }
}
