<?php

namespace App\Serializer\Opendata\Enveloppe;

use App\Database\Opendata\Enveloppe\PontThermique\XMLPontThermiqueReader;
use App\Domain\Enveloppe\Entity\PontThermique;
use App\Domain\Enveloppe\Enveloppe;
use App\Domain\Enveloppe\ValueObject\PontThermique\Liaison;
use Webmozart\Assert\Assert;

final class XMLPontThermiqueTransformer
{
    public function to(XMLPontThermiqueReader $reader, Enveloppe $entity): PontThermique
    {
        $mur = $entity->murs()->find($reader->mur_id());
        Assert::notNull($mur, "Mur non trouvé pour le pont thermique {$reader->reference()}");

        $paroi = null;

        if ($reader->paroi_id()) {
            $paroi = $entity->paroi($reader->paroi_id());
            Assert::notNull($paroi, "Paroi non trouvée pour le pont thermique {$reader->reference()}");
        }
        return PontThermique::create(
            id: $reader->id(),
            enveloppe: $entity,
            description: $reader->description(),
            longueur: $reader->longueur(),
            kpt: $reader->k_saisi(),
            liaison: Liaison::create(
                type: $reader->type_liaison(),
                pont_thermique_partiel: $reader->pont_thermique_partiel(),
                mur: $mur,
                paroi: $paroi,
            ),
        );
    }
}
