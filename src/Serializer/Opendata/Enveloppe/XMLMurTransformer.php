<?php

namespace App\Serializer\Opendata\Enveloppe;

use App\Database\Opendata\Enveloppe\Mur\XMLMurReader;
use App\Domain\Enveloppe\Entity\Mur;
use App\Domain\Enveloppe\Enveloppe;
use App\Domain\Enveloppe\ValueObject\Mur\Position;
use Webmozart\Assert\Assert;

final class XMLMurTransformer
{
    public function to(XMLMurReader $reader, Enveloppe $entity): Mur
    {
        $local_non_chauffe = null;

        if ($reader->local_non_chauffe_id()) {
            $local_non_chauffe = $entity->locaux_non_chauffes()->find($reader->local_non_chauffe_id());
            Assert::notNull($local_non_chauffe, "Local non chauffé {$reader->local_non_chauffe_id()} non trouvé");
        }
        return Mur::create(
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
            position: Position::create(
                surface: $reader->surface(),
                mitoyennete: $reader->mitoyennete(),
                orientation: $reader->orientation(),
                local_non_chauffe: $local_non_chauffe,
            ),
            isolation: $reader->isolation(),
        );
    }
}
