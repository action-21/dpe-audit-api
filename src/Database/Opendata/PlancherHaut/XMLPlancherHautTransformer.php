<?php

namespace App\Database\Opendata\PlancherHaut;

use App\Database\Opendata\Lnc\XMLLncTransformer;
use App\Database\Opendata\XMLElement;
use App\Domain\Enveloppe\Enveloppe;
use App\Domain\PlancherHaut\{PlancherHaut, PlancherHautCollection};
use App\Domain\PlancherHaut\ValueObject\{Caracteristique, Isolation, Position};

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
                    mitoyennete: $reader->mitoyennete(),
                ),
                caracteristique: new Caracteristique(
                    type: $reader->type_plancher_haut(),
                    inertie: $reader->inertie(),
                    surface: $reader->surface(),
                    annee_construction: null,
                    annee_renovation: null,
                    u0: $reader->uph0_saisi(),
                    u: $reader->uph_saisi(),
                ),
                isolation: new Isolation(
                    etat_isolation: $reader->etat_isolation(),
                    type_isolation: $reader->type_isolation(),
                    annee_isolation: $reader->annee_isolation(),
                    epaisseur_isolation: $reader->epaisseur_isolation(),
                    resistance_thermique_isolation: $reader->resistance_isolation(),
                ),
            );
            $entity->initialise();
            $enveloppe->parois()->add_plancher_haut($entity);
        }
        return $enveloppe->parois()->planchers_hauts();
    }
}
