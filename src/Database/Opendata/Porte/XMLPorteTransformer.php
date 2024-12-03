<?php

namespace App\Database\Opendata\Porte;

use App\Database\Opendata\Lnc\XMLLncTransformer;
use App\Database\Opendata\XMLElement;
use App\Domain\Enveloppe\Enveloppe;
use App\Domain\Porte\{Porte, PorteCollection};

final class XMLPorteTransformer
{
    public function __construct(private XMLLncTransformer $lnc_transformer,) {}

    public function transform(XMLElement $root, Enveloppe $enveloppe): PorteCollection
    {
        foreach ($root->read_enveloppe()->read_portes() as $reader) {
            $paroi_id = $reader->paroi_id() ? $enveloppe->parois()->get($reader->paroi_id())?->id() : null;
            $lnc = null === $paroi_id ? $this->lnc_transformer->transform($reader->xml(), $enveloppe) : null;

            for ($i = 1; $i <= $reader->quantite(); $i++) {
                $entity = new Porte(
                    id: $reader->id(),
                    enveloppe: $enveloppe,
                    description: $reader->description(),
                    caracteristique: $reader->caracteristique(),
                    position: $reader->position(
                        paroi_id: $paroi_id,
                        local_non_chauffe_id: $lnc?->id(),
                    ),
                );
                $enveloppe->parois()->portes()->add($entity);
            }
        }
        return $enveloppe->parois()->portes();
    }
}
