<?php

namespace App\Database\Opendata\PlancherBas;

use App\Database\Opendata\Lnc\XMLLncTransformer;
use App\Database\Opendata\XMLElement;
use App\Domain\Enveloppe\Enveloppe;
use App\Domain\PlancherBas\{PlancherBas, PlancherBasCollection};
use App\Domain\PlancherBas\ValueObject\Position;

final class XMLPlancherBasTransformer
{
    public function __construct(private XMLLncTransformer $lnc_transformer,) {}

    public function transform(XMLElement $root, Enveloppe $enveloppe): PlancherBasCollection
    {
        foreach ($root->read_enveloppe()->read_planchers_bas() as $reader) {
            $lnc = $this->lnc_transformer->transform($reader->xml(), $enveloppe);

            $entity = new PlancherBas(
                id: $reader->id(),
                enveloppe: $enveloppe,
                description: $reader->description(),
                position: new Position(
                    local_non_chauffe_id: $lnc?->id(),
                    mitoyennete: $reader->mitoyennete(),
                ),
                caracteristique: $reader->caracteristique(),
                isolation: $reader->isolation(),
            );

            $enveloppe->parois()->add_plancher_bas($entity);
        }
        return $enveloppe->parois()->planchers_bas();
    }
}
