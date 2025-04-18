<?php

namespace App\Database\Opendata\Enveloppe;

use App\Database\Opendata\Enveloppe\Baie\XMLBaieDeserializer;
use App\Database\Opendata\Enveloppe\Lnc\XMLLncDeserializer;
use App\Database\Opendata\Enveloppe\Mur\XMLMurDeserializer;
use App\Database\Opendata\Enveloppe\Niveau\XMLNiveauDeserializer;
use App\Database\Opendata\Enveloppe\PlancherBas\XMLPlancherBasDeserializer;
use App\Database\Opendata\Enveloppe\PlancherHaut\XMLPlancherHautDeserializer;
use App\Database\Opendata\Enveloppe\PontThermique\XMLPontThermiqueDeserializer;
use App\Database\Opendata\Enveloppe\Porte\XMLPorteDeserializer;
use App\Database\Opendata\XMLElement;
use App\Domain\Enveloppe\Enveloppe;

final class XMLEnveloppeDeserializer
{
    private XMLEnveloppeReader $reader;
    private Enveloppe $entity;

    public function __construct(
        private readonly XMLNiveauDeserializer $niveau_deserializer,
        private readonly XMLLncDeserializer $local_non_chauffe_deserializer,
        private readonly XMLMurDeserializer $mur_deserializer,
        private readonly XMLPlancherBasDeserializer $plancher_bas_deserializer,
        private readonly XMLPlancherHautDeserializer $plancher_haut_deserializer,
        private readonly XMLBaieDeserializer $baie_deserializer,
        private readonly XMLPorteDeserializer $porte_deserializer,
        private readonly XMLPontThermiqueDeserializer $pont_thermique_deserializer,
    ) {}

    public function deserialize(XMLElement $xml): Enveloppe
    {
        $this->reader = XMLEnveloppeReader::from($xml);
        $this->entity = Enveloppe::create(
            exposition: $this->reader->exposition(),
            q4pa_conv: $this->reader->q4pa_conv(),
        );

        foreach ($this->reader->niveaux() as $item) {
            $this->entity->add_niveau(
                $this->niveau_deserializer->deserialize($item->xml(), $this->entity)
            );
        }
        foreach ($this->reader->locaux_non_chauffes() as $item) {
            $this->entity->add_local_non_chauffe(
                $this->local_non_chauffe_deserializer->deserialize($item->xml(), $this->entity)
            );
        }
        foreach ($this->reader->murs() as $item) {
            $this->entity->add_mur(
                $this->mur_deserializer->deserialize($item->xml(), $this->entity)
            );
        }
        foreach ($this->reader->planchers_bas() as $item) {
            $this->entity->add_plancher_bas(
                $this->plancher_bas_deserializer->deserialize($item->xml(), $this->entity)
            );
        }
        foreach ($this->reader->planchers_hauts() as $item) {
            $this->entity->add_plancher_haut(
                $this->plancher_haut_deserializer->deserialize($item->xml(), $this->entity)
            );
        }
        foreach ($this->reader->baies() as $item) {
            $this->entity->add_baie(
                $this->baie_deserializer->deserialize($item->xml(), $this->entity)
            );
        }
        foreach ($this->reader->portes() as $item) {
            $this->entity->add_porte(
                $this->porte_deserializer->deserialize($item->xml(), $this->entity)
            );
        }
        foreach ($this->reader->ponts_thermiques() as $item) {
            $this->entity->add_pont_thermique(
                $this->pont_thermique_deserializer->deserialize($item->xml(), $this->entity)
            );
        }
        return $this->entity;
    }
}
