<?php

namespace App\Database\Opendata\Mur;

use App\Database\Opendata\Lnc\XMLLncTransformer;
use App\Database\Opendata\XMLElement;
use App\Domain\Enveloppe\Enveloppe;
use App\Domain\Mur\{Mur, MurCollection};
use App\Domain\Mur\ValueObject\Position;

final class XMLMurTransformer
{
    public function __construct(private XMLLncTransformer $lnc_transformer,) {}

    public function transform(XMLElement $root, Enveloppe $enveloppe): MurCollection
    {
        foreach ($root->read_enveloppe()->read_murs() as $reader) {
            $lnc = $this->lnc_transformer->transform($reader->xml(), $enveloppe);

            $entity = new Mur(
                id: $reader->id(),
                enveloppe: $enveloppe,
                description: $reader->description(),
                position: new Position(
                    local_non_chauffe_id: $lnc?->id(),
                    mitoyennete: $reader->mitoyennete(),
                    orientation: $reader->orientation(),
                ),
                caracteristique: $reader->caracteristique(),
                isolation: $reader->isolation(),
            );

            $enveloppe->parois()->add_mur($entity);
        }
        return $enveloppe->parois()->murs();
    }
}
