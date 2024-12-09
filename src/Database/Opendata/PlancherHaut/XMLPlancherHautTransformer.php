<?php

namespace App\Database\Opendata\PlancherHaut;

use App\Database\Opendata\Lnc\XMLLncTransformer;
use App\Database\Opendata\XMLElement;
use App\Domain\Enveloppe\Enveloppe;
use App\Domain\PlancherHaut\{PlancherHaut, PlancherHautCollection};
use App\Domain\PlancherHaut\ValueObject\Position;

final class XMLPlancherHautTransformer
{
    public function __construct(private XMLLncTransformer $lnc_transformer,) {}

    public function transform(XMLElement $root, Enveloppe $enveloppe): PlancherHautCollection
    {
        foreach ($root->read_enveloppe()->read_planchers_hauts() as $reader) {
            $lnc = $this->lnc_transformer->transform($reader->xml(), $enveloppe);

            $entity = new PlancherHaut(
                id: $reader->id(),
                enveloppe: $enveloppe,
                description: $reader->description(),
                position: new Position(
                    local_non_chauffe_id: $lnc?->id(),
                    orientation: null,
                    mitoyennete: $reader->mitoyennete(),
                ),
                caracteristique: $reader->caracteristique(),
                isolation: $reader->isolation(),
            );
            $entity->initialise();
            $enveloppe->parois()->add_plancher_haut($entity);
        }
        return $enveloppe->parois()->planchers_hauts();
    }
}
