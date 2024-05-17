<?php

namespace App\Database\Opendata\PontThermique;

use App\Database\Opendata\XMLElement;
use App\Domain\Enveloppe\Enveloppe;
use App\Domain\Paroi\Enum\TypeParoi;
use App\Domain\PontThermique\{PontThermique, PontThermiqueCollection};

final class XMLPontThermiqueParser
{
    public function __construct(private XMLPontThermiqueReader $reader,)
    {
    }

    public function parse(XMLElement $xml, Enveloppe &$enveloppe): PontThermiqueCollection
    {
        foreach ($this->reader->read($xml) as $reader) {
            $mur = null;
            $plancher = null;
            $ouverture = null;

            if ($reader->reference_1()) {
                $mur = $enveloppe->mur_collection(TypeParoi::MUR)->find($reader->reference_1());
                $plancher = $enveloppe->plancher_haut_collection()->find($reader->reference_1());
                $plancher = $plancher ?? $enveloppe->plancher_bas_collection()->find($reader->reference_1());
                $ouverture = $enveloppe->paroi_collection()->search_ouverture()->find($reader->reference_1());
            }
            if ($reader->reference_2()) {
                $mur = $mur ?? $enveloppe->mur_collection(TypeParoi::MUR)->find($reader->reference_2());
                $plancher = $plancher ?? $enveloppe->plancher_haut_collection()->find($reader->reference_2());
                $plancher = $plancher ?? $enveloppe->plancher_bas_collection()->find($reader->reference_2());
                $ouverture = $ouverture ?? $enveloppe->paroi_collection()->search_ouverture()->find($reader->reference_2());
            }

            $entity = new PontThermique(
                id: $reader->id(),
                enveloppe: $enveloppe,
                type_liaison: $reader->enum_type_liaision(),
                description: $reader->description(),
                longueur: $reader->l(),
                valeur: $reader->k_saisi(),
                pont_thermique_partiel: $reader->pont_thermique_partiel(),
                mur: $mur,
                plancher: $plancher,
                ouverture: $ouverture,
            );

            $enveloppe->pont_thermique_collection()->add($entity);
        }
        return $enveloppe->pont_thermique_collection();
    }
}
