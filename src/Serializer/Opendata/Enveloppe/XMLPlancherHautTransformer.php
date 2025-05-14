<?php

namespace App\Serializer\Opendata\Enveloppe;

use App\Database\Opendata\Enveloppe\PlancherHaut\XMLPlancherHautReader;
use App\Domain\Enveloppe\Entity\PlancherHaut;
use App\Domain\Enveloppe\Enveloppe;
use App\Domain\Enveloppe\ValueObject\PlancherHaut\Position;
use Webmozart\Assert\Assert;

final class XMLPlancherHautTransformer
{
    public function to(XMLPlancherHautReader $reader, Enveloppe $entity): PlancherHaut
    {
        $local_non_chauffe = null;

        if ($reader->local_non_chauffe_id()) {
            $local_non_chauffe = $entity->locaux_non_chauffes()->find($reader->local_non_chauffe_id());
            Assert::notNull($local_non_chauffe, "Local non chauffé {$reader->local_non_chauffe_id()} non trouvé");
        }
        return PlancherHaut::create(
            id: $reader->id(),
            enveloppe: $entity,
            description: $reader->description(),
            configuration: $reader->configuration(),
            type_structure: $reader->type_structure(),
            inertie: $reader->inertie(),
            annee_construction: $reader->annee_construction(),
            annee_renovation: $reader->annee_renovation(),
            u0: $reader->uph0_saisi(),
            u: $reader->uph_saisi(),
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
