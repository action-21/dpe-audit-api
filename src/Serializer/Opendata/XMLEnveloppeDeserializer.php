<?php

namespace App\Serializer\Opendata;

use App\Database\Opendata\Enveloppe\XMLEnveloppeReader;
use App\Database\Opendata\XMLElement;
use App\Serializer\Opendata\Enveloppe\XMLBaieTransformer;
use App\Serializer\Opendata\Enveloppe\XMLLncTransformer;
use App\Serializer\Opendata\Enveloppe\XMLMurTransformer;
use App\Serializer\Opendata\Enveloppe\XMLNiveauTransformer;
use App\Serializer\Opendata\Enveloppe\XMLPlancherBasTransformer;
use App\Serializer\Opendata\Enveloppe\XMLPlancherHautTransformer;
use App\Serializer\Opendata\Enveloppe\XMLPontThermiqueTransformer;
use App\Serializer\Opendata\Enveloppe\XMLPorteTransformer;
use App\Domain\Enveloppe\Enveloppe;

final class XMLEnveloppeDeserializer
{
    private XMLEnveloppeReader $reader;
    private Enveloppe $entity;

    public function __construct(
        private readonly XMLNiveauTransformer $niveau_transformer,
        private readonly XMLLncTransformer $local_non_chauffe_transformer,
        private readonly XMLMurTransformer $mur_transformer,
        private readonly XMLPlancherBasTransformer $plancher_bas_transformer,
        private readonly XMLPlancherHautTransformer $plancher_haut_transformer,
        private readonly XMLBaieTransformer $baie_transformer,
        private readonly XMLPorteTransformer $porte_transformer,
        private readonly XMLPontThermiqueTransformer $pont_thermique_transformer,
    ) {}

    public function deserialize(XMLElement $xml): Enveloppe
    {
        $this->reader = XMLEnveloppeReader::from($xml);
        $this->entity = Enveloppe::create(
            exposition: $this->reader->exposition(),
            q4pa_conv: $this->reader->q4pa_conv(),
            presence_brasseurs_air: $this->reader->presence_brasseurs_air(),
        );

        foreach ($this->reader->niveaux() as $item) {
            $this->entity->add_niveau(
                $this->niveau_transformer->to($item, $this->entity)
            );
        }
        foreach ($this->reader->locaux_non_chauffes() as $item) {
            $this->entity->add_local_non_chauffe(
                $this->local_non_chauffe_transformer->to($item, $this->entity)
            );
        }
        foreach ($this->reader->murs() as $item) {
            $this->entity->add_mur(
                $this->mur_transformer->to($item, $this->entity)
            );
        }
        foreach ($this->reader->planchers_bas() as $item) {
            $this->entity->add_plancher_bas(
                $this->plancher_bas_transformer->to($item, $this->entity)
            );
        }
        foreach ($this->reader->planchers_hauts() as $item) {
            $this->entity->add_plancher_haut(
                $this->plancher_haut_transformer->to($item, $this->entity)
            );
        }
        foreach ($this->reader->baies() as $item) {
            $this->entity->add_baie(
                $this->baie_transformer->to($item, $this->entity)
            );
        }
        foreach ($this->reader->portes() as $item) {
            $this->entity->add_porte(
                $this->porte_transformer->to($item, $this->entity)
            );
        }
        foreach ($this->reader->ponts_thermiques() as $item) {
            $this->entity->add_pont_thermique(
                $this->pont_thermique_transformer->to($item, $this->entity)
            );
        }
        return $this->entity;
    }
}
