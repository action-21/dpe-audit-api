<?php

namespace App\Database\Opendata\Enveloppe\Lnc;

use App\Database\Opendata\XMLElement;
use App\Domain\Enveloppe\Data\Lnc\{BaieData, ParoiOpaqueData};
use App\Domain\Enveloppe\Data\LncData;
use App\Domain\Enveloppe\Entity\Lnc;
use App\Domain\Enveloppe\Entity\Lnc\{Baie, BaieCollection, ParoiOpaque, ParoiOpaqueCollection};
use App\Domain\Enveloppe\Enveloppe;
use App\Domain\Enveloppe\ValueObject\Lnc\{PositionBaie, PositionParoi};

final class XMLEtsDeserializer
{
    private XMLEtsReader $reader;

    public function deserialize(XMLElement $xml, Enveloppe $entity): Lnc
    {
        $this->reader = XMLEtsReader::from($xml);

        $lnc = new Lnc(
            id: $this->reader->id(),
            enveloppe: $entity,
            description: $this->reader->description(),
            type: $this->reader->type(),
            parois_opaques: new ParoiOpaqueCollection,
            baies: new BaieCollection,
            data: LncData::create(),
        );

        foreach ($this->reader->parois_opaques() as $reader) {
            $lnc->add_paroi_opaque(new ParoiOpaque(
                id: $reader->id(),
                local_non_chauffe: $lnc,
                description: $reader->description(),
                isolation: $reader->isolation(),
                position: new PositionParoi(
                    mitoyennete: $reader->mitoyennete(),
                    surface: $reader->surface(),
                ),
                data: ParoiOpaqueData::create(),
            ));
        }
        foreach ($this->reader->baies() as $reader) {
            $lnc->add_baie(new Baie(
                id: $reader->id(),
                local_non_chauffe: $lnc,
                description: $reader->description(),
                type: $reader->type(),
                materiau: $reader->materiau(),
                type_vitrage: $reader->type_vitrage(),
                presence_rupteur_pont_thermique: $reader->presence_rupteur_pont_thermique(),
                position: new PositionBaie(
                    mitoyennete: $reader->mitoyennete(),
                    surface: $reader->surface(),
                    inclinaison: $reader->inclinaison(),
                    orientation: $reader->orientation(),
                    paroi: null,
                ),
                data: BaieData::create(),
            ));
        }
        return $lnc;
    }
}
