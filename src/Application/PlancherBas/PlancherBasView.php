<?php

namespace App\Application\PlancherBas;

use App\Domain\Common\Enum\Enum;
use App\Domain\Common\Table\TableValue;
use App\Domain\PlancherBas\{PlancherBas, PlancherBasCollection};
use App\Domain\PlancherBas\{PlancherBasEngine, PlancherBasEngineCollection};

class PlancherBasView
{
    public function __construct(
        public readonly string $id,
        public readonly ?string $reference_local_non_chauffe,
        public readonly string $description,
        public readonly Enum $mitoyennete,
        public readonly Enum $type_plancher_bas,
        public readonly Enum $inertie,
        public readonly float $surface,
        public readonly float $perimetre,
        public readonly ?float $upb0_saisi,
        public readonly ?float $upb_saisi,
        public readonly Enum $type_isolation,
        public readonly ?int $annnee_isolation,
        public readonly ?float $epaisseur_isolant,
        public readonly ?float $resistance_thermique,
        public readonly ?float $orientation,
        public readonly ?float $dp = null,
        public readonly ?float $upb0 = null,
        public readonly ?float $upb = null,
        public readonly ?float $ue = null,
        public readonly ?float $upbfinal = null,
        public readonly ?float $b = null,
        public readonly ?float $sdep = null,
        public readonly ?Enum $qualite_isolation = null,
        public readonly ?TableValue $table_b = null,
        public readonly ?TableValue $table_upb0 = null,
        public readonly ?TableValue $table_upb = null,
        /** @var ?array<TableValue> */
        public readonly ?array $table_ue_collection = null,
    ) {
    }

    public static function from_entity(PlancherBas $entity): self
    {
        return new self(
            id: $entity->id(),
            reference_local_non_chauffe: $entity->local_non_chauffe()?->id(),
            description: $entity->description(),
            mitoyennete: $entity->mitoyennete(),
            type_plancher_bas: $entity->caracteristique()->type_plancher_bas,
            inertie: $entity->caracteristique()->inertie,
            surface: $entity->caracteristique()->surface->valeur(),
            perimetre: $entity->caracteristique()->perimetre->valeur(),
            upb0_saisi: $entity->caracteristique()->upb0?->valeur(),
            upb_saisi: $entity->caracteristique()->upb?->valeur(),
            type_isolation: $entity->isolation()->type_isolation,
            annnee_isolation: $entity->isolation()->annnee_isolation?->valeur(),
            epaisseur_isolant: $entity->isolation()->epaisseur_isolant?->valeur(),
            resistance_thermique: $entity->isolation()->resistance_thermique?->valeur(),
            orientation: $entity->orientation()?->valeur(),
        );
    }

    /** @return self[] */
    public static function from_entity_collection(PlancherBasCollection $collection): array
    {
        return \array_map(fn (PlancherBas $entity) => self::from_entity($entity), $collection->to_array());
    }

    public static function from_engine(PlancherBasEngine $engine): self
    {
        $entity = $engine->input();
        return new self(
            id: $entity->id(),
            reference_local_non_chauffe: $entity->local_non_chauffe()?->id(),
            description: $entity->description(),
            mitoyennete: $entity->mitoyennete(),
            type_plancher_bas: $entity->caracteristique()->type_plancher_bas,
            inertie: $entity->caracteristique()->inertie,
            surface: $entity->caracteristique()->surface->valeur(),
            perimetre: $entity->caracteristique()->perimetre->valeur(),
            upb0_saisi: $entity->caracteristique()->upb0?->valeur(),
            upb_saisi: $entity->caracteristique()->upb?->valeur(),
            type_isolation: $entity->isolation()->type_isolation,
            annnee_isolation: $entity->isolation()->annnee_isolation?->valeur(),
            epaisseur_isolant: $entity->isolation()->epaisseur_isolant?->valeur(),
            resistance_thermique: $entity->isolation()->resistance_thermique?->valeur(),
            orientation: $entity->orientation()?->valeur(),
            dp: $engine->dp(),
            upb0: $engine->u0(),
            upb: $engine->u(),
            ue: $engine->ue(),
            upbfinal: $engine->ufinal(),
            b: $engine->b(),
            sdep: $engine->sdep(),
            qualite_isolation: $engine->qualite_isolation(),
            table_b: $engine->table_b(),
            table_upb0: $engine->table_upb0(),
            table_upb: $engine->table_upb(),
            table_ue_collection: $engine->table_ue_collection()->to_array(),
        );
    }

    /** @return self[] */
    public static function from_engine_collection(PlancherBasEngineCollection $collection): array
    {
        return \array_map(fn (PlancherBasEngine $engine) => self::from_engine($engine), $collection->liste());
    }
}
