<?php

namespace App\Api\Enveloppe;

use App\Api\Baie\CreateBaieHandler;
use App\Api\Enveloppe\Payload\EnveloppePayload;
use App\Api\Lnc\CreateLncHandler;
use App\Api\Mur\CreateMurHandler;
use App\Api\PlancherBas\CreatePlancherBasHandler;
use App\Api\PlancherHaut\CreatePlancherHautHandler;
use App\Api\PontThermique\CreatePontThermiqueHandler;
use App\Api\Porte\CreatePorteHandler;
use App\Domain\Audit\Audit;
use App\Domain\Enveloppe\Entity\{PlancherIntermediaire, Refend};
use App\Domain\Enveloppe\Enveloppe;

final class CreateEnveloppeHandler
{
    public function __construct(
        private CreateLncHandler $lnc_handler,
        private CreateMurHandler $mur_handler,
        private CreatePlancherBasHandler $plancher_bas_handler,
        private CreatePlancherHautHandler $plancher_haut_handler,
        private CreatePorteHandler $porte_handler,
        private CreateBaieHandler $baie_handler,
        private CreatePontThermiqueHandler $pont_thermique_handler,
    ) {}

    public function __invoke(EnveloppePayload $payload, Audit $audit,): Enveloppe
    {
        $enveloppe = Enveloppe::create(
            audit: $audit,
            exposition: $payload->exposition,
            q4pa_conv: $payload->q4pa_conv,
        );

        foreach ($payload->locaux_non_chauffes as $item) {
            $enveloppe->add_local_non_chauffe($this->lnc_handler->__invoke($item, $enveloppe));
        }
        foreach ($payload->murs as $item) {
            $enveloppe->parois()->add_mur($this->mur_handler->__invoke($item, $enveloppe));
        }
        foreach ($payload->planchers_bas as $item) {
            $enveloppe->parois()->add_plancher_bas($this->plancher_bas_handler->__invoke($item, $enveloppe));
        }
        foreach ($payload->planchers_hauts as $item) {
            $enveloppe->parois()->add_plancher_haut($this->plancher_haut_handler->__invoke($item, $enveloppe));
        }
        foreach ($payload->baies as $item) {
            $enveloppe->parois()->add_baie($this->baie_handler->__invoke($item, $enveloppe));
        }
        foreach ($payload->portes as $item) {
            $enveloppe->parois()->add_porte($this->porte_handler->__invoke($item, $enveloppe));
        }
        foreach ($payload->ponts_thermiques as $item) {
            $enveloppe->add_pont_thermique($this->pont_thermique_handler->__invoke($item, $enveloppe));
        }
        foreach ($payload->planchers_intermediaires as $item) {
            $enveloppe->add_plancher_intermediaire(PlancherIntermediaire::create(
                enveloppe: $enveloppe,
                description: $item->description,
                surface: $item->surface,
                inertie: $item->inertie,
            ));
        }
        foreach ($payload->refends as $item) {
            $enveloppe->add_refend(Refend::create(
                enveloppe: $enveloppe,
                description: $item->description,
                surface: $item->surface,
                inertie: $item->inertie,
            ));
        }
        return $enveloppe;
    }
}
