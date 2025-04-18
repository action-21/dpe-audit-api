<?php

namespace App\Database\Opendata\Enveloppe\Porte;

use App\Database\Opendata\XMLElement;
use App\Domain\Enveloppe\Data\PorteData;
use App\Domain\Enveloppe\Enveloppe;
use App\Domain\Enveloppe\Entity\Porte;
use App\Domain\Enveloppe\ValueObject\Porte\{Menuiserie, Position, Vitrage};
use Webmozart\Assert\Assert;

final class XMLPorteDeserializer
{
    public function deserialize(XMLElement $xml, Enveloppe $entity): Porte
    {
        $reader = XMLPorteReader::from($xml);

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

        return new Porte(
            id: $reader->id(),
            enveloppe: $entity,
            description: $reader->description(),
            type_pose: $reader->type_pose(),
            isolation: $reader->isolation(),
            materiau: $reader->materiau(),
            presence_sas: $reader->presence_sas(),
            annee_installation: $reader->annee_installation(),
            u: $reader->u(),
            position: new Position(
                surface: $reader->surface(),
                mitoyennete: $reader->mitoyennete(),
                orientation: $reader->orientation(),
                paroi: $paroi,
                local_non_chauffe: $local_non_chauffe,
            ),
            menuiserie: new Menuiserie(
                presence_joint: $reader->presence_joint(),
                presence_retour_isolation: $reader->presence_retour_isolation(),
                largeur_dormant: $reader->largeur_dormant(),
            ),
            vitrage: new Vitrage(
                taux_vitrage: $reader->taux_vitrage(),
                type_vitrage: $reader->type_vitrage(),
            ),
            data: PorteData::create(),
        );
    }
}
