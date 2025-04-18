<?php

namespace App\Database\Opendata\Enveloppe\Mur;

use App\Database\Opendata\XMLElement;
use App\Domain\Enveloppe\Data\MurData;
use App\Domain\Enveloppe\Entity\Mur;
use App\Domain\Enveloppe\Enveloppe;
use App\Domain\Enveloppe\ValueObject\Isolation;
use App\Domain\Enveloppe\ValueObject\Mur\Position;
use Webmozart\Assert\Assert;

final class XMLMurDeserializer
{
    public function deserialize(XMLElement $xml, Enveloppe $entity): Mur
    {
        $reader = XMLMurReader::from($xml);

        $local_non_chauffe = null;

        if ($reader->local_non_chauffe_id()) {
            $local_non_chauffe = $entity->locaux_non_chauffes()->find($reader->local_non_chauffe_id());
            Assert::notNull($local_non_chauffe, "Local non chauffé {$reader->local_non_chauffe_id()} non trouvé");
        }
        return new Mur(
            id: $reader->id(),
            enveloppe: $entity,
            description: $reader->description(),
            type_structure: $reader->type_structure(),
            type_doublage: $reader->type_doublage(),
            epaisseur_structure: $reader->epaisseur_structure(),
            presence_enduit_isolant: $reader->presence_enduit_isolant(),
            paroi_ancienne: $reader->paroi_ancienne(),
            inertie: $reader->inertie(),
            annee_construction: $reader->annee_construction(),
            annee_renovation: $reader->annee_renovation(),
            u0: $reader->umur0_saisi(),
            u: $reader->umur_saisi(),
            position: new Position(
                surface: $reader->surface(),
                mitoyennete: $reader->mitoyennete(),
                orientation: $reader->orientation(),
                local_non_chauffe: $local_non_chauffe,
            ),
            isolation: new Isolation(
                etat_isolation: $reader->etat_isolation(),
                type_isolation: $reader->type_isolation(),
                annee_isolation: $reader->annee_isolation(),
                epaisseur_isolation: $reader->epaisseur_isolation(),
                resistance_thermique_isolation: $reader->resistance_thermique_isolation(),
            ),
            data: MurData::create(),
        );
    }
}
