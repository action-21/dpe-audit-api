<?php

namespace App\Database\Opendata\Enveloppe;

use App\Database\Opendata\Audit\XMLAuditTransformer;
use App\Database\Opendata\Baie\XMLBaieTransformer;
use App\Database\Opendata\Mur\XMLMurTransformer;
use App\Database\Opendata\PlancherBas\XMLPlancherBasTransformer;
use App\Database\Opendata\PlancherHaut\XMLPlancherHautTransformer;
use App\Database\Opendata\PontThermique\XMLPontThermiqueTransformer;
use App\Database\Opendata\Porte\XMLPorteTransformer;
use App\Database\Opendata\XMLElement;
use App\Domain\Enveloppe\Enveloppe;

final class XMLEnveloppeTransformer
{
    public function __construct(
        private XMLMurTransformer $mur_transformer,
        private XMLPlancherBasTransformer $plancher_bas_transformer,
        private XMLPlancherHautTransformer $plancher_haut_transformer,
        private XMLBaieTransformer $baie_transformer,
        private XMLPorteTransformer $porte_transformer,
        private XMLPontThermiqueTransformer $pont_thermique_transformer,
        private XMLAuditTransformer $audit_transformer,
    ) {}

    public function transform(XMLElement $root): Enveloppe
    {
        $audit = $this->audit_transformer->transform($root);
        $reader = $root->read_enveloppe();

        $enveloppe = Enveloppe::create(
            audit: $audit,
            exposition: $reader->exposition(),
            q4pa_conv: $reader->q4pa_conv(),
        );

        $this->mur_transformer->transform($root, $enveloppe);
        $this->plancher_bas_transformer->transform($root, $enveloppe);
        $this->plancher_haut_transformer->transform($root, $enveloppe);
        $this->baie_transformer->transform($root, $enveloppe);
        $this->porte_transformer->transform($root, $enveloppe);
        $this->pont_thermique_transformer->transform($root, $enveloppe);
        return $enveloppe;
    }
}
