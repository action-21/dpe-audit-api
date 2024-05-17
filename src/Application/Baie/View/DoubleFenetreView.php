<?php

namespace App\Application\Baie\View;

use App\Domain\Baie\Engine\DoubleFenetreEngine;
use App\Domain\Baie\ValueObject\DoubleFenetre;
use App\Domain\Common\Enum\Enum;
use App\Domain\Common\Table\TableValue;

class DoubleFenetreView
{
    public function __construct(
        public readonly Enum $type_baie,
        public readonly Enum $type_pose,
        public readonly Enum $nature_menuiserie,
        public readonly Enum $type_vitrage,
        public readonly float $inclinaison_vitrage,
        public readonly ?float $epaisseur_lame,
        public readonly ?Enum $nature_gaz_lame,
        public readonly ?float $ug_saisi,
        public readonly ?float $uw_saisi,
        public readonly ?float $sw_saisi,
        public readonly ?float $ug = null,
        public readonly ?float $uw = null,
        public readonly ?float $sw = null,
        public readonly ?TableValue $table_sw = null,
        /** @var ?arrau<TableValue> */
        public ?array $table_ug_collection = null,
        /** @var ?arrau<TableValue> */
        public ?array $table_uw_collection = null,
    ) {
    }

    public static function from_vo(DoubleFenetre $vo): self
    {
        return new self(
            type_baie: $vo->type_baie,
            type_pose: $vo->type_pose,
            nature_menuiserie: $vo->nature_menuiserie,
            type_vitrage: $vo->type_vitrage,
            inclinaison_vitrage: $vo->inclinaison_vitrage->valeur(),
            epaisseur_lame: $vo->epaisseur_lame?->valeur(),
            nature_gaz_lame: $vo->nature_gaz_lame,
            ug_saisi: $vo->ug?->valeur(),
            uw_saisi: $vo->uw?->valeur(),
            sw_saisi: $vo->sw?->valeur(),
        );
    }

    public static function from_engine(DoubleFenetreEngine $engine): self
    {
        $vo = $engine->input();
        return new self(
            type_baie: $vo->type_baie,
            type_pose: $vo->type_pose,
            nature_menuiserie: $vo->nature_menuiserie,
            type_vitrage: $vo->type_vitrage,
            inclinaison_vitrage: $vo->inclinaison_vitrage->valeur(),
            epaisseur_lame: $vo->epaisseur_lame?->valeur(),
            nature_gaz_lame: $vo->nature_gaz_lame,
            ug_saisi: $vo->ug?->valeur(),
            uw_saisi: $vo->uw?->valeur(),
            sw_saisi: $vo->sw?->valeur(),
            ug: $engine->ug(),
            uw: $engine->uw(),
            sw: $engine->sw(),
            table_sw: $engine->table_sw(),
            table_ug_collection: $engine->table_ug_collection()->to_array(),
            table_uw_collection: $engine->table_uw_collection()->to_array(),
        );
    }
}
