<?php

namespace App\Database\Opendata\MasqueLointain;

use App\Database\Opendata\XMLElement;
use App\Domain\Enveloppe\Enveloppe;
use App\Domain\MasqueLointain\{MasqueLointain, MasqueLointainCollection};

final class XMLMasqueLointainParser
{
    public function __construct(private XMLMasqueLointainReader $reader)
    {
    }

    public function parse(XMLElement $xml, Enveloppe &$enveloppe): MasqueLointainCollection
    {
        foreach ($this->reader->read($xml) as $reader) {
            foreach ($reader->masque_lointain_homogene_reader() as $masque_lointain_homogene_reader) {
                $entity = new MasqueLointain(
                    id: $masque_lointain_homogene_reader->id(),
                    enveloppe: $enveloppe,
                    description: $masque_lointain_homogene_reader->description(),
                    type_masque: $masque_lointain_homogene_reader->type_masque_lointain(),
                    hauteur_alpha: $masque_lointain_homogene_reader->hauteur_alpha(),
                    orientation: $masque_lointain_homogene_reader->orientation(),
                    secteur_orientation: $masque_lointain_homogene_reader->secteur_orientation(),
                );
                $enveloppe->masque_lointain_collection()->add($entity);
            }
            foreach ($reader->masque_lointain_non_homogene_reader() as $masque_lointain_non_homogene_reader) {
                $entity = new MasqueLointain(
                    id: $masque_lointain_non_homogene_reader->id(),
                    enveloppe: $enveloppe,
                    description: $masque_lointain_non_homogene_reader->description(),
                    type_masque: $masque_lointain_non_homogene_reader->type_masque_lointain(),
                    hauteur_alpha: $masque_lointain_non_homogene_reader->hauteur_alpha(),
                    orientation: $masque_lointain_non_homogene_reader->orientation(),
                    secteur_orientation: $masque_lointain_non_homogene_reader->secteur_orientation(),
                );
                $enveloppe->masque_lointain_collection()->add($entity);
            }
        }
        return $enveloppe->masque_lointain_collection();
    }
}
