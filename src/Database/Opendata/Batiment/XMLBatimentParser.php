<?php

namespace App\Database\Opendata\Batiment;

use App\Database\Opendata\Enveloppe\XMLEnveloppeParser;
use App\Database\Opendata\Logement\XMLLogementParser;
use App\Database\Opendata\XMLElement;
use App\Domain\Audit\Audit;
use App\Domain\Batiment\Batiment;
use App\Domain\Batiment\Entity\{Niveau, NiveauCollection};
use App\Domain\Common\Identifier\Uuid;
use App\Domain\Logement\LogementCollection;

final class XMLBatimentParser
{
    public function __construct(
        private XMLBatimentReader $batiment_reader,
        private XMLLogementParser $logement_parser,
        private XMLEnveloppeParser $enveloppe_parser,
    ) {
    }

    public function parse(XMLElement $xml, Audit &$audit): Batiment
    {
        $reader = $this->batiment_reader->read($xml);

        $entity = new Batiment(
            id: $reader->id(),
            audit: $audit,
            type_batiment: $audit->perimetre_application()->type_batiment(),
            adresse: $reader->adresse(),
            caracteristique: $reader->caracteristique(),
            enveloppe: null,
            niveau_collection: new NiveauCollection,
            logement_collection: new LogementCollection,
        );

        foreach ($reader->niveau_reader() as $niveau_reader) {
            $entity->niveau_collection()->add(new Niveau(
                id: Uuid::create(),
                batiment: $entity,
                surface_habitable: $niveau_reader->surface_habitable(),
                hauteur_sous_plafond: $niveau_reader->hauteur_sous_plafond(),
            ));
        }

        $this->logement_parser->parse($xml, $entity);
        $this->enveloppe_parser->parse($xml, $entity);
        $audit->set_batiment($entity);
        return $entity;
    }
}
