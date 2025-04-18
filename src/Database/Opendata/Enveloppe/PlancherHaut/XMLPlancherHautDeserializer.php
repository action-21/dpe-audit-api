<?php

namespace App\Database\Opendata\Enveloppe\PlancherHaut;

use App\Database\Opendata\XMLElement;
use App\Domain\Enveloppe\Data\PlancherHautData;
use App\Domain\Enveloppe\Entity\PlancherHaut;
use App\Domain\Enveloppe\Enveloppe;
use App\Domain\Enveloppe\ValueObject\Isolation;
use App\Domain\Enveloppe\ValueObject\PlancherHaut\Position;
use Webmozart\Assert\Assert;

final class XMLPlancherHautDeserializer
{
    public function deserialize(XMLElement $xml, Enveloppe $entity): PlancherHaut
    {
        $reader = XMLPlancherHautReader::from($xml);

        $local_non_chauffe = null;

        if ($reader->local_non_chauffe_id()) {
            $local_non_chauffe = $entity->locaux_non_chauffes()->find($reader->local_non_chauffe_id());
            Assert::notNull($local_non_chauffe, "Local non chauffé {$reader->local_non_chauffe_id()} non trouvé");
        }
        return new PlancherHaut(
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
            data: PlancherHautData::create(),
        );
    }
}
