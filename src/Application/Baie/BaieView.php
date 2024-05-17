<?php

namespace App\Application\Baie;

use App\Application\Baie\View\DoubleFenetreView;
use App\Domain\Baie\{Baie, BaieCollection, BaieEngine, BaieEngineCollection};
use App\Domain\Common\Enum\{Enum, Mois};
use App\Domain\Common\Table\TableValue;

class BaieView
{
    public function __construct(
        public readonly string $id,
        public readonly ?string $reference_paroi_opaque,
        public readonly ?string $reference_local_non_chauffe,
        public readonly string $description,
        public readonly Enum $mitoyennete,
        public readonly float $surface,
        public readonly float $orientation,
        public readonly float $largeur_dormant,
        public readonly bool $presence_joint,
        public readonly bool $presence_retour_isolation,
        public readonly Enum $type_baie,
        public readonly Enum $type_pose,
        public readonly Enum $nature_menuiserie,
        public readonly Enum $type_vitrage,
        public readonly Enum $type_fermeture,
        public readonly float $inclinaison_vitrage,
        public readonly ?float $epaisseur_lame,
        public readonly ?Enum $nature_gaz_lame,
        public readonly ?float $ug_saisi,
        public readonly ?float $uw_saisi,
        public readonly ?float $ujn_saisi,
        public readonly ?float $sw_saisi,
        public readonly ?DoubleFenetreView $double_fenetre,
        public readonly ?float $dp = null,
        public readonly ?float $ubaie = null,
        public readonly ?float $b = null,
        public readonly ?float $bver = null,
        public readonly ?float $sdep = null,
        public readonly ?float $ug = null,
        public readonly ?float $uw = null,
        public readonly ?float $uw1 = null,
        public readonly ?float $uw2 = null,
        public readonly ?float $deltar = null,
        public readonly ?float $ujn = null,
        public readonly ?float $t = null,
        public readonly ?float $fe = null,
        public readonly ?float $fe1 = null,
        public readonly ?float $fe2 = null,
        public readonly ?float $sw = null,
        public readonly ?float $sw1 = null,
        public readonly ?float $sw2 = null,
        public readonly ?float $sse = null,
        /** @var ?array<float> */
        public readonly ?array $sse_j = null,
        /** @var ?array<float> */
        public readonly ?array $ssind_j = null,
        /** @var ?array<float> */
        public readonly ?array $sst_j = null,
        /** @var ?array<float> */
        public readonly ?array $ssd_j = null,
        /** @var ?array<float> */
        public readonly ?array $c1_j = null,
        public readonly ?Enum $qualite_isolation = null,
        public readonly ?TableValue $table_b = null,
        public readonly ?TableValue $table_sw = null,
        public readonly ?TableValue $table_deltar = null,
        /** @var ?arrau<TableValue> */
        public ?array $table_ug_collection = null,
        /** @var ?arrau<TableValue> */
        public ?array $table_uw_collection = null,
        /** @var ?arrau<TableValue> */
        public ?array $table_ujn_collection = null,
    ) {
    }

    public static function from_entity(Baie $entity): self
    {
        return new self(
            id: $entity->id(),
            reference_paroi_opaque: $entity->paroi_opaque()?->id(),
            reference_local_non_chauffe: $entity->local_non_chauffe()?->id(),
            description: $entity->description(),
            mitoyennete: $entity->mitoyennete(),
            surface: $entity->surface(),
            orientation: $entity->orientation()->valeur(),
            largeur_dormant: $entity->largeur_dormant(),
            presence_joint: $entity->presence_joint(),
            presence_retour_isolation: $entity->presence_retour_isolation(),
            type_pose: $entity->type_pose(),
            type_baie: $entity->caracteristique()->type_baie,
            nature_menuiserie: $entity->caracteristique()->nature_menuiserie,
            type_vitrage: $entity->caracteristique()->type_vitrage,
            type_fermeture: $entity->caracteristique()->type_fermeture,
            inclinaison_vitrage: $entity->caracteristique()->inclinaison_vitrage->valeur(),
            epaisseur_lame: $entity->caracteristique()->epaisseur_lame?->valeur(),
            nature_gaz_lame: $entity->caracteristique()->nature_gaz_lame,
            ug_saisi: $entity->caracteristique()->ug?->valeur(),
            uw_saisi: $entity->caracteristique()->uw?->valeur(),
            ujn_saisi: $entity->caracteristique()->ujn?->valeur(),
            sw_saisi: $entity->caracteristique()->sw?->valeur(),
            double_fenetre: ($vo = $entity->double_fenetre()) ? DoubleFenetreView::from_vo($vo) : null,
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
            reference_paroi_opaque: $entity->paroi_opaque()?->id(),
            reference_local_non_chauffe: $entity->local_non_chauffe()?->id(),
            description: $entity->description(),
            mitoyennete: $entity->mitoyennete(),
            surface: $entity->surface(),
            orientation: $entity->orientation()->valeur(),
            largeur_dormant: $entity->largeur_dormant(),
            presence_joint: $entity->presence_joint(),
            presence_retour_isolation: $entity->presence_retour_isolation(),
            type_pose: $entity->type_pose(),
            type_baie: $entity->caracteristique()->type_baie,
            nature_menuiserie: $entity->caracteristique()->nature_menuiserie,
            type_vitrage: $entity->caracteristique()->type_vitrage,
            type_fermeture: $entity->caracteristique()->type_fermeture,
            inclinaison_vitrage: $entity->caracteristique()->inclinaison_vitrage->valeur(),
            epaisseur_lame: $entity->caracteristique()->epaisseur_lame?->valeur(),
            nature_gaz_lame: $entity->caracteristique()->nature_gaz_lame,
            ug_saisi: $entity->caracteristique()->ug?->valeur(),
            uw_saisi: $entity->caracteristique()->uw?->valeur(),
            ujn_saisi: $entity->caracteristique()->ujn?->valeur(),
            sw_saisi: $entity->caracteristique()->sw?->valeur(),
            double_fenetre: $engine->double_fenetre_engine() ? DoubleFenetreView::from_engine($engine->double_fenetre_engine()) : null,
            dp: $engine->dp(),
            ubaie: $engine->u(),
            b: $engine->b(),
            bver: $engine->bver(),
            sdep: $engine->sdep(),
            ug: $engine->ug(),
            uw: $engine->uw(),
            uw1: $engine->uw1(),
            uw2: $engine->uw2(),
            deltar: $engine->deltar(),
            ujn: $engine->ujn(),
            t: $engine->t(),
            fe: $engine->fe(),
            fe1: $engine->fe1(),
            fe2: $engine->fe2(),
            sw: $engine->sw(),
            sw1: $engine->sw1(),
            sw2: $engine->sw2(),
            sse: $engine->sse(),
            sse_j: \array_map(fn (Mois $mois) => $engine->sse_j($mois), Mois::cases()),
            ssind_j: \array_map(fn (Mois $mois) => $engine->ssind_j($mois), Mois::cases()),
            sst_j: \array_map(fn (Mois $mois) => $engine->sst_j($mois), Mois::cases()),
            ssd_j: \array_map(fn (Mois $mois) => $engine->ssd_j($mois), Mois::cases()),
            c1_j: \array_map(fn (Mois $mois) => $engine->c1_j($mois), Mois::cases()),
            qualite_isolation: $engine->qualite_isolation(),
            table_b: $engine->table_b(),
            table_sw: $engine->table_sw(),
            table_deltar: $engine->table_deltar(),
            table_ug_collection: $engine->table_ug_collection()->to_array(),
            table_uw_collection: $engine->table_uw_collection()->to_array(),
            table_ujn_collection: $engine->table_ujn_collection()->to_array(),
        );
    }

    /** @return self[] */
    public static function from_engine_collection(BaieEngineCollection $collection): array
    {
        return \array_map(fn (BaieEngine $engine) => self::from_engine($engine), $collection->liste());
    }
}
