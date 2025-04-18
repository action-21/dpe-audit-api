<?php

namespace App\Database\Opendata\Enveloppe\Lnc;

use App\Database\Opendata\XMLElement;
use App\Domain\Enveloppe\Data\Lnc\ParoiOpaqueData;
use App\Domain\Enveloppe\Data\LncData;
use App\Domain\Enveloppe\Entity\Lnc;
use App\Domain\Enveloppe\Entity\Lnc\{BaieCollection, ParoiOpaque, ParoiOpaqueCollection};
use App\Domain\Enveloppe\Enveloppe;
use App\Domain\Enveloppe\ValueObject\Lnc\PositionParoi;

final class XMLLncVirtuelDeserializer
{
    private XMLLncVirtuelReader $reader;

    public function deserialize(XMLElement $xml, Enveloppe $entity): Lnc
    {
        $this->reader = XMLLncVirtuelReader::from($xml);

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
        return $lnc;
    }
}
