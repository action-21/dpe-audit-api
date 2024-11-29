<?php

namespace App\Database\Opendata\PlancherBas;

use App\Database\Opendata\Lnc\XMLLncTransformer;
use App\Database\Opendata\XMLElement;
use App\Domain\Enveloppe\Enveloppe;
use App\Domain\PlancherBas\{PlancherBas, PlancherBasCollection};
use App\Domain\PlancherBas\ValueObject\{Caracteristique, Isolation, Position};

final class XMLPlancherBasTransformer
{
    public function __construct(private XMLLncTransformer $lnc_transformer,) {}

    public function transform(XMLElement $root, Enveloppe $enveloppe): PlancherBasCollection
    {
        foreach ($root->read_planchers_bas() as $reader) {
            $lnc = $this->lnc_transformer->transform($reader->xml(), $enveloppe);

            $entity = new PlancherBas(
                id: $reader->id(),
                enveloppe: $enveloppe,
                description: $reader->description(),
                position: new Position(
                    local_non_chauffe_id: $lnc?->id(),
                    mitoyennete: $reader->mitoyennete(),
                ),
                caracteristique: new Caracteristique(
                    type: $reader->type_plancher_bas(),
                    inertie: $reader->inertie(),
                    perimetre: $reader->perimetre(),
                    surface: $reader->surface(),
                    annee_construction: null,
                    annee_renovation: null,
                    u0: $reader->upb0_saisi(),
                    u: $reader->upb_saisi(),
                ),
                isolation: new Isolation(
                    etat_isolation: $reader->etat_isolation(),
                    type_isolation: $reader->type_isolation(),
                    annee_isolation: $reader->annee_isolation(),
                    epaisseur_isolation: $reader->epaisseur_isolation(),
                    resistance_thermique_isolation: $reader->resistance_isolation(),
                ),
            );

            $enveloppe->parois()->add_plancher_bas($entity);
        }
        return $enveloppe->parois()->planchers_bas();
    }
}
