<?php

namespace App\Database\Opendata\Baie;

use App\Database\Opendata\Lnc\XMLLncTransformer;
use App\Database\Opendata\XMLElement;
use App\Domain\Baie\{Baie, BaieCollection};
use App\Domain\Baie\Entity\{MasqueLointain, MasqueLointainCollection, MasqueProche, MasqueProcheCollection};
use App\Domain\Baie\ValueObject\{Position};
use App\Domain\Enveloppe\Enveloppe;

final class XMLBaieTransformer
{
    public function __construct(
        private XMLBaieReader $reader_iterator,
        private XMLLncTransformer $lnc_transformer,
    ) {}

    public function transform(XMLElement $root, Enveloppe $enveloppe): BaieCollection
    {
        foreach ($this->reader_iterator->read($root->baie_collection()) as $reader) {
            $lnc = null === $reader->paroi_id() ? $this->lnc_transformer->transform($reader->xml(), $enveloppe) : null;

            for ($i = 1; $i <= $reader->nb_baie(); $i++) {
                $entity = new Baie(
                    id: $reader->id(),
                    enveloppe: $enveloppe,
                    description: $reader->description(),
                    position: new Position(
                        paroi_id: $reader->paroi_id(),
                        local_non_chauffe_id: $lnc?->id(),
                        mitoyennete: $reader->mitoyennete(),
                        orientation: $reader->orientation(),
                    ),
                    caracteristique: $reader->caracteristique(),
                    double_fenetre: $reader->double_fenetre(),
                    masques_proches: new MasqueProcheCollection,
                    masques_lointains: new MasqueLointainCollection,
                );
                $this->set_masques_proches($reader, $entity);
                $this->set_masques_lointains_homogenes($reader, $entity);
                $this->set_masques_lointains_non_homogenes($reader, $entity);
                $enveloppe->parois()->add_baie($entity);
            }
        }
        return $enveloppe->parois()->baies();
    }

    private function set_masques_proches(XMLBaieReader $reader, Baie $entity): void
    {
        if ($masque_proche_reader = $reader->read_masque_proche()) {
            $entity->add_masque_proche(new MasqueProche(
                id: $masque_proche_reader->id(),
                baie: $entity,
                description: $masque_proche_reader->description(),
                type_masque: $masque_proche_reader->type_masque(),
                avancee: $masque_proche_reader->avancee(),
            ));
        }
    }

    private function set_masques_lointains_homogenes(XMLBaieReader $reader, Baie $entity): void
    {
        if ($masque_lointain_reader = $reader->read_masque_lointain_homogene()) {
            $entity->add_masque_lointain(new MasqueLointain(
                id: $masque_lointain_reader->id(),
                baie: $entity,
                description: $masque_lointain_reader->description(),
                type_masque: $masque_lointain_reader->type_masque(),
                hauteur: $masque_lointain_reader->hauteur(),
                orientation: $entity->orientation(),
            ));
        }
    }

    private function set_masques_lointains_non_homogenes(XMLBaieReader $reader, Baie $entity): void
    {
        foreach ($reader->read_masque_lointain_non_homogene() as $masque_lointain_reader) {
            $entity->add_masque_lointain(new MasqueLointain(
                id: $masque_lointain_reader->id(),
                baie: $entity,
                description: $masque_lointain_reader->description(),
                type_masque: $masque_lointain_reader->type_masque(),
                hauteur: $masque_lointain_reader->hauteur(),
                orientation: $masque_lointain_reader->orientation($entity->orientation()),
            ));
        }
    }
}
