<?php

namespace App\Database\Opendata\Lnc;

use App\Database\Opendata\XMLElement;
use App\Domain\Enveloppe\Enveloppe;
use App\Domain\Lnc\{Lnc, LncCollection};
use App\Domain\Lnc\Entity\{Baie, BaieCollection, ParoiOpaque, ParoiOpaqueCollection};

final class XMLLncParser
{
    public function __construct(
        private XMLLncReader $lnc_reader,
        private XMLEtsReader $ets_reader,
    ) {
    }

    public function parse(XMLElement $xml, Enveloppe &$enveloppe): LncCollection
    {
        $this->set_ets_collection($xml, $enveloppe);
        $this->set_lnc_collection($xml, $enveloppe);
        return $enveloppe->lnc_collection();
    }

    private function set_lnc_collection(XMLElement $xml, Enveloppe &$enveloppe): void
    {
        foreach ($this->lnc_reader->read($xml) as $reader) {
            if ($enveloppe->lnc_collection()->search_by_type_lnc($reader->type_lnc())->search_by_surface_paroi($reader->surface_aue())->count() > 0) {
                continue;
            }
            $entity = new Lnc(
                id: $reader->id(),
                enveloppe: $enveloppe,
                description: 'Local non chauffé',
                type_lnc: $reader->type_lnc(),
                baie_collection: new BaieCollection,
                paroi_opaque_collection: new ParoiOpaqueCollection,
            );
            foreach ($reader->paroi_reader() as $paroi_reader) {
                $entity->paroi_opaque_collection()->add(new ParoiOpaque(
                    id: $paroi_reader->id(),
                    local_non_chauffe: $entity,
                    description: $paroi_reader->description(),
                    surface: $paroi_reader->surface(),
                    isolation: $paroi_reader->isolation(),
                ));
            }
            $enveloppe->lnc_collection()->add($entity);
        }
    }

    private function set_ets_collection(XMLElement $xml, Enveloppe &$enveloppe): void
    {
        foreach ($this->ets_reader->read($xml) as $reader) {
            $entity = new Lnc(
                id: $reader->id(),
                enveloppe: $enveloppe,
                description: $reader->description(),
                type_lnc: $reader->type_lnc(),
                baie_collection: new BaieCollection,
                paroi_opaque_collection: new ParoiOpaqueCollection,
            );

            foreach ($reader->baie_reader() as $baie_reader) {
                for ($k = 1; $k <= $baie_reader->nombre(); $k++) {
                    $entity->baie_collection()->add(new Baie(
                        id: $baie_reader->id(),
                        local_non_chauffe: $entity,
                        description: $baie_reader->description(),
                        surface: $baie_reader->surface(),
                        nature_menuiserie: $baie_reader->enum_nature_menuiserie(),
                        inclinaison_vitrage: $baie_reader->inclinaison_vitrage(),
                        orientation: $baie_reader->orientation(),
                        type_vitrage: $baie_reader->enum_type_vitrage(),
                        vitrage_vir: $baie_reader->vitrage_vir(),
                    ));
                }
            }
            $enveloppe->lnc_collection()->add($entity);
        }
    }
}
