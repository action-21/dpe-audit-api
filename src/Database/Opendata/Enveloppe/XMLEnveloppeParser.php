<?php

namespace App\Database\Opendata\Enveloppe;

use App\Database\Opendata\Baie\XMLBaieParser;
use App\Database\Opendata\Lnc\XMLLncParser;
use App\Database\Opendata\MasqueLointain\XMLMasqueLointainParser;
use App\Database\Opendata\MasqueProche\XMLMasqueProcheParser;
use App\Database\Opendata\Mur\XMLMurParser;
use App\Database\Opendata\PlancherBas\XMLPlancherBasParser;
use App\Database\Opendata\PlancherHaut\XMLPlancherHautParser;
use App\Database\Opendata\PontThermique\XMLPontThermiqueParser;
use App\Database\Opendata\Porte\XMLPorteParser;
use App\Database\Opendata\XMLElement;
use App\Domain\Batiment\Batiment;
use App\Domain\Enveloppe\Enveloppe;

final class XMLEnveloppeParser
{
    public function __construct(
        private XMLEnveloppeReader $reader,
        private XMLLncParser $lnc_parser,
        private XMLMurParser $mur_parser,
        private XMLPlancherHautParser $plancher_haut_parser,
        private XMLPlancherBasParser $plancher_bas_parser,
        private XMLBaieParser $baie_parser,
        private XMLPorteParser $porte_parser,
        private XMLMasqueProcheParser $masque_proche_parser,
        private XMLMasqueLointainParser $masque_lointain_parser,
        private XMLPontThermiqueParser $pont_thermique_parser,
    ) {
    }

    public function parse(XMLElement $xml, Batiment &$batiment): Enveloppe
    {
        $reader = $this->reader->read($xml);
        $enveloppe = Enveloppe::create(batiment: $batiment, permeabilite: $reader->permeabilite());

        $this->lnc_parser->parse($xml, $enveloppe);
        $this->mur_parser->parse($xml, $enveloppe);
        $this->plancher_haut_parser->parse($xml, $enveloppe);
        $this->plancher_bas_parser->parse($xml, $enveloppe);
        $this->baie_parser->parse($xml, $enveloppe);
        $this->porte_parser->parse($xml, $enveloppe);
        $this->masque_proche_parser->parse($xml, $enveloppe);
        $this->masque_lointain_parser->parse($xml, $enveloppe);
        $this->pont_thermique_parser->parse($xml, $enveloppe);

        $batiment->set_enveloppe($enveloppe);
        return $enveloppe;
    }
}
