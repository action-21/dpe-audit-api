<?php

namespace App\Serializer\Opendata\Enveloppe;

use App\Database\Opendata\Enveloppe\PlancherBas\XMLPlancherBasReader;
use App\Domain\Enveloppe\Entity\PlancherBas;
use App\Domain\Enveloppe\Enveloppe;
use App\Domain\Enveloppe\ValueObject\PlancherBas\Position;
use Webmozart\Assert\Assert;

final class XMLPlancherBasTransformer
{
    public function to(XMLPlancherBasReader $reader, Enveloppe $entity): PlancherBas
    {
        $local_non_chauffe = null;

        if ($reader->local_non_chauffe_id()) {
            $local_non_chauffe = $entity->locaux_non_chauffes()->find($reader->local_non_chauffe_id());
            Assert::notNull($local_non_chauffe, "Local non chauffé {$reader->local_non_chauffe_id()} non trouvé");
        }
        return PlancherBas::create(
            id: $reader->id(),
            enveloppe: $entity,
            description: $reader->description(),
            type_structure: $reader->type_structure(),
            inertie: $reader->inertie(),
            annee_construction: $reader->annee_construction(),
            annee_renovation: $reader->annee_renovation(),
            u0: $reader->upb0_saisi(),
            u: $reader->upb_saisi(),
            position: Position::create(
                surface: $reader->surface(),
                perimetre: $reader->perimetre(),
                mitoyennete: $reader->mitoyennete(),
                local_non_chauffe: $local_non_chauffe,
            ),
            isolation: $reader->isolation(),
        );
    }
}
