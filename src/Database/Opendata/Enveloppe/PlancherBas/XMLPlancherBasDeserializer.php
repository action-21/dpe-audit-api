<?php

namespace App\Database\Opendata\Enveloppe\PlancherBas;

use App\Database\Opendata\XMLElement;
use App\Domain\Enveloppe\Data\PlancherBasData;
use App\Domain\Enveloppe\Entity\PlancherBas;
use App\Domain\Enveloppe\Enveloppe;
use App\Domain\Enveloppe\ValueObject\Isolation;
use App\Domain\Enveloppe\ValueObject\PlancherBas\Position;
use Webmozart\Assert\Assert;

final class XMLPlancherBasDeserializer
{
    public function deserialize(XMLElement $xml, Enveloppe $entity): PlancherBas
    {
        $reader = XMLPlancherBasReader::from($xml);

        $local_non_chauffe = null;

        if ($reader->local_non_chauffe_id()) {
            $local_non_chauffe = $entity->locaux_non_chauffes()->find($reader->local_non_chauffe_id());
            Assert::notNull($local_non_chauffe, "Local non chauffé {$reader->local_non_chauffe_id()} non trouvé");
        }
        return new PlancherBas(
            id: $reader->id(),
            enveloppe: $entity,
            description: $reader->description(),
            type_structure: $reader->type_structure(),
            inertie: $reader->inertie(),
            annee_construction: $reader->annee_construction(),
            annee_renovation: $reader->annee_renovation(),
            u0: $reader->upb0_saisi(),
            u: $reader->upb_saisi(),
            position: new Position(
                surface: $reader->surface(),
                perimetre: $reader->perimetre(),
                mitoyennete: $reader->mitoyennete(),
                local_non_chauffe: $local_non_chauffe,
            ),
            isolation: new Isolation(
                etat_isolation: $reader->etat_isolation(),
                type_isolation: $reader->type_isolation(),
                annee_isolation: $reader->annee_isolation(),
                epaisseur_isolation: $reader->epaisseur_isolation(),
                resistance_thermique_isolation: $reader->resistance_thermique_isolation(),
            ),
            data: PlancherBasData::create(),
        );
    }
}
