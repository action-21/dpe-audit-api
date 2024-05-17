<?php

namespace App\Application\Porte;

use App\Domain\Common\Enum\Enum;
use App\Domain\Common\Table\TableValue;
use App\Domain\Porte\{Porte, PorteCollection, PorteEngine, PorteEngineCollection};

class PorteView
{
    public function __construct(
        public readonly string $id,
        public readonly string $enveloppe_id,
        public readonly ?string $paroi_opaque_id,
        public readonly ?string $local_non_chauffe_id,
        public readonly string $description,
        public readonly float $surface,
        public readonly Enum $mitoyennete,
        public readonly Enum $type_pose,
        public readonly Enum $nature_menuiserie,
        public readonly Enum $type_porte,
        public readonly bool $presence_joint,
        public readonly ?bool $presence_retour_isolation,
        public readonly ?float $largeur_dormant,
        public readonly ?float $uporte_saisi,
        public readonly ?float $dp = null,
        public readonly ?float $uporte = null,
        public readonly ?float $b = null,
        public readonly ?float $sdep = null,
        public readonly ?Enum $qualite_isolation = null,
        public readonly ?TableValue $table_b = null,
        public readonly ?TableValue $table_uporte = null,
    ) {
    }

    public static function from_entity(Porte $entity): self
    {
        return new self(
            id: $entity->id(),
            enveloppe_id: $entity->enveloppe()->id(),
            paroi_opaque_id: $entity->paroi_opaque()?->id(),
            local_non_chauffe_id: $entity->local_non_chauffe()?->id(),
            description: $entity->description(),
            mitoyennete: $entity->mitoyennete(),
            surface: $entity->surface(),
            type_pose: $entity->type_pose(),
            nature_menuiserie: $entity->caracteristique()->nature_menuiserie,
            type_porte: $entity->caracteristique()->type_porte,
            presence_joint: $entity->presence_joint(),
            presence_retour_isolation: $entity->presence_retour_isolation(),
            largeur_dormant: $entity->largeur_dormant(),
            uporte_saisi: $entity->caracteristique()->uporte?->valeur(),
        );
    }

    /** @return self[] */
    public static function from_entity_collection(PorteCollection $collection): array
    {
        return \array_map(fn (Porte $entity) => self::from_entity($entity), $collection->to_array());
    }

    public static function from_engine(PorteEngine $engine): self
    {
        $entity = $engine->input();

        return new self(
            id: $entity->id(),
            enveloppe_id: $entity->enveloppe()->id(),
            paroi_opaque_id: $entity->paroi_opaque()?->id(),
            local_non_chauffe_id: $entity->local_non_chauffe()?->id(),
            description: $entity->description(),
            mitoyennete: $entity->mitoyennete(),
            surface: $entity->surface(),
            type_pose: $entity->type_pose(),
            nature_menuiserie: $entity->caracteristique()->nature_menuiserie,
            type_porte: $entity->caracteristique()->type_porte,
            presence_joint: $entity->presence_joint(),
            presence_retour_isolation: $entity->presence_retour_isolation(),
            largeur_dormant: $entity->largeur_dormant(),
            uporte_saisi: $entity->caracteristique()->uporte?->valeur(),
            dp: $engine->dp(),
            uporte: $engine->u(),
            b: $engine->b(),
            sdep: $engine->sdep(),
            qualite_isolation: $engine->qualite_isolation(),
            table_b: $engine->table_b(),
            table_uporte: $engine->table_uporte(),
        );
    }

    /** @return self[] */
    public static function from_engine_collection(PorteEngineCollection $collection): array
    {
        return \array_map(fn (PorteEngine $engine) => self::from_engine($engine), $collection->liste());
    }
}
