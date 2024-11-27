<?php

namespace App\Database\Opendata\Porte;

use App\Database\Opendata\Lnc\XMLLncTransformer;
use App\Database\Opendata\XMLElement;
use App\Domain\Enveloppe\Enveloppe;
use App\Domain\Porte\{Porte, PorteCollection};
use App\Domain\Porte\ValueObject\{Caracteristique, Position};

final class XMLPorteTransformer
{
    public function __construct(
        private XMLPorteReader $reader_iterator,
        private XMLLncTransformer $lnc_transformer,
    ) {}

    public function transform(XMLElement $root, Enveloppe $enveloppe): PorteCollection
    {
        foreach ($this->reader_iterator->read($root->porte_collection()) as $reader) {
            $lnc = null === $reader->paroi_id() ? $this->lnc_transformer->transform($reader->xml(), $enveloppe) : null;

            for ($i = 1; $i <= $reader->quantite(); $i++) {
                $entity = new Porte(
                    id: $reader->id(),
                    enveloppe: $enveloppe,
                    description: $reader->description(),
                    caracteristique: new Caracteristique(
                        surface: $reader->surface(),
                        isolation: $reader->isolation(),
                        nature_menuiserie: $reader->nature_menuiserie(),
                        type_pose: $reader->type_pose(),
                        taux_vitrage: $reader->taux_vitrage(),
                        largeur_dormant: $reader->largeur_dormant(),
                        presence_sas: $reader->presence_sas(),
                        presence_joint: $reader->presence_joint(),
                        presence_retour_isolation: $reader->presence_retour_isolation(),
                        annee_installation: null,
                        type_vitrage: $reader->type_vitrage(),
                        u: $reader->u_saisi(),
                    ),
                    position: new Position(
                        paroi_id: $reader->paroi_id(),
                        local_non_chauffe_id: $lnc?->id(),
                        mitoyennete: $reader->mitoyennete(),
                    ),
                );
                $enveloppe->parois()->portes()->add($entity);
            }
        }
        return $enveloppe->parois()->portes();
    }
}
