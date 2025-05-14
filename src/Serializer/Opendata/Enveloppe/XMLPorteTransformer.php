<?php

namespace App\Serializer\Opendata\Enveloppe;

use App\Database\Opendata\Enveloppe\Porte\XMLPorteReader;
use App\Domain\Enveloppe\Enveloppe;
use App\Domain\Enveloppe\Entity\Porte;
use App\Domain\Enveloppe\ValueObject\Porte\Position;
use Webmozart\Assert\Assert;

final class XMLPorteTransformer
{
    public function to(XMLPorteReader $reader, Enveloppe $entity): Porte
    {
        $paroi = null;
        $local_non_chauffe = null;

        if ($reader->paroi_id()) {
            $paroi = $entity->paroi($reader->paroi_id());
            Assert::notNull($paroi, "Paroi {$reader->paroi_id()} non trouvée");
        }
        if ($reader->local_non_chauffe_id()) {
            $local_non_chauffe = $entity->locaux_non_chauffes()->find($reader->local_non_chauffe_id());
            Assert::notNull($local_non_chauffe, "Local non chauffé {$reader->local_non_chauffe_id()} non trouvé");
        }
        return Porte::create(
            id: $reader->id(),
            enveloppe: $entity,
            description: $reader->description(),
            type_pose: $reader->type_pose(),
            isolation: $reader->isolation(),
            materiau: $reader->materiau(),
            presence_sas: $reader->presence_sas(),
            annee_installation: $reader->annee_installation(),
            u: $reader->u(),
            position: Position::create(
                surface: $reader->surface(),
                mitoyennete: $reader->mitoyennete(),
                orientation: $reader->orientation(),
                paroi: $paroi,
                local_non_chauffe: $local_non_chauffe,
            ),
            menuiserie: $reader->menuiserie(),
            vitrage: $reader->vitrage(),
        );
    }
}
