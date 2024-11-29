<?php

namespace App\Database\Opendata\Mur;

use App\Database\Opendata\Lnc\XMLLncTransformer;
use App\Database\Opendata\XMLElement;
use App\Domain\Enveloppe\Enveloppe;
use App\Domain\Mur\{Mur, MurCollection};
use App\Domain\Mur\ValueObject\{Caracteristique, Isolation, Position};

final class XMLMurTransformer
{
    public function __construct(private XMLLncTransformer $lnc_transformer,) {}

    public function transform(XMLElement $root, Enveloppe $enveloppe): MurCollection
    {
        foreach ($root->read_murs() as $reader) {
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
                caracteristique: new Caracteristique(
                    type: $reader->type_mur(),
                    type_doublage: $reader->type_doublage(),
                    inertie: $reader->inertie(),
                    surface: $reader->surface(),
                    presence_enduit_isolant: $reader->presence_enduit_isolant(),
                    paroi_ancienne: $reader->paroi_ancienne(),
                    epaisseur: $reader->epaisseur(),
                    annee_construction: null,
                    annee_renovation: null,
                    u0: $reader->umur0_saisi(),
                    u: $reader->umur_saisi(),
                ),
                isolation: new Isolation(
                    etat_isolation: $reader->etat_isolation(),
                    type_isolation: $reader->type_isolation(),
                    annee_isolation: $reader->annee_isolation(),
                    epaisseur_isolation: $reader->epaisseur_isolation(),
                    resistance_thermique_isolation: $reader->resistance_isolation(),
                ),
            );

            $enveloppe->parois()->add_mur($entity);
        }
        return $enveloppe->parois()->murs();
    }
}
