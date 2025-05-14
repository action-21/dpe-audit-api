<?php

namespace App\Serializer\Opendata\Enveloppe;

use App\Database\Opendata\Enveloppe\Lnc\XMLLncReader;
use App\Domain\Enveloppe\Entity\Lnc;
use App\Domain\Enveloppe\Entity\Lnc\{Baie, ParoiOpaque};
use App\Domain\Enveloppe\Enveloppe;
use App\Domain\Enveloppe\ValueObject\Lnc\{PositionBaie, PositionParoi};

final class XMLLncTransformer
{
    public function to(XMLLncReader $reader, Enveloppe $entity): Lnc
    {
        $lnc = Lnc::create(
            id: $reader->id(),
            enveloppe: $entity,
            description: $reader->description(),
            type: $reader->type(),
        );

        foreach ($reader->parois_opaques() as $paroi_reader) {
            $lnc->add_paroi_opaque(ParoiOpaque::create(
                id: $paroi_reader->id(),
                local_non_chauffe: $lnc,
                description: $paroi_reader->description(),
                isolation: $paroi_reader->isolation(),
                position: PositionParoi::create(
                    mitoyennete: $paroi_reader->mitoyennete(),
                    surface: $paroi_reader->surface(),
                ),
            ));
        }

        foreach ($reader->baies() as $paroi_reader) {
            $lnc->add_baie(Baie::create(
                id: $paroi_reader->id(),
                local_non_chauffe: $lnc,
                description: $paroi_reader->description(),
                type: $paroi_reader->type(),
                materiau: $paroi_reader->materiau(),
                type_vitrage: $paroi_reader->type_vitrage(),
                presence_rupteur_pont_thermique: $paroi_reader->presence_rupteur_pont_thermique(),
                position: PositionBaie::create(
                    mitoyennete: $paroi_reader->mitoyennete(),
                    surface: $paroi_reader->surface(),
                    inclinaison: $paroi_reader->inclinaison(),
                    orientation: $paroi_reader->orientation(),
                    paroi: null,
                ),
            ));
        }

        return $lnc;
    }
}
