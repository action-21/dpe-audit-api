<?php

namespace App\Api\Enveloppe;

use App\Api\Enveloppe\Handler\{CreateBaieHandler, CreateLncHandler, CreateMurHandler, CreateNiveauHandler, CreatePlancherBasHandler, CreatePlancherHautHandler, CreatePontThermiqueHandler, CreatePorteHandler};
use App\Api\Enveloppe\Model\Enveloppe as Payload;
use App\Domain\Enveloppe\Enveloppe;

final class CreateEnveloppeHandler
{
    public function __construct(
        private CreateLncHandler $lnc_handler,
        private CreateNiveauHandler $niveau_handler,
        private CreateBaieHandler $baie_handler,
        private CreateMurHandler $mur_handler,
        private CreatePlancherBasHandler $plancher_bas_handler,
        private CreatePlancherHautHandler $plancher_haut_handler,
        private CreatePorteHandler $porte_handler,
        private CreatePontThermiqueHandler $pont_thermique_handler,
    ) {}

    public function __invoke(Payload $payload): Enveloppe
    {
        $entity = Enveloppe::create(
            exposition: $payload->exposition,
            q4pa_conv: $payload->q4pa_conv,
        );

        foreach ($payload->locaux_non_chauffes as $local_non_chauffe_payload) {
            $handler = $this->lnc_handler;
            $entity->add_local_non_chauffe($handler(payload: $local_non_chauffe_payload, entity: $entity));
        }
        foreach ($payload->niveaux as $niveau_payload) {
            $handler = $this->niveau_handler;
            $entity->add_niveau($handler(payload: $niveau_payload, entity: $entity));
        }
        foreach ($payload->murs as $mur_payload) {
            $handler = $this->mur_handler;
            $entity->add_mur($handler(payload: $mur_payload, entity: $entity));
        }
        foreach ($payload->planchers_bas as $plancher_bas_payload) {
            $handler = $this->plancher_bas_handler;
            $entity->add_plancher_bas($handler(payload: $plancher_bas_payload, entity: $entity));
        }
        foreach ($payload->planchers_hauts as $plancher_haut_payload) {
            $handler = $this->plancher_haut_handler;
            $entity->add_plancher_haut($handler(payload: $plancher_haut_payload, entity: $entity));
        }
        foreach ($payload->baies as $baie_payload) {
            $handler = $this->baie_handler;
            $entity->add_baie($handler(payload: $baie_payload, entity: $entity));
        }
        foreach ($payload->portes as $porte_payload) {
            $handler = $this->porte_handler;
            $entity->add_porte($handler(payload: $porte_payload, entity: $entity));
        }
        foreach ($payload->ponts_thermiques as $pont_thermique_payload) {
            $handler = $this->pont_thermique_handler;
            $entity->add_pont_thermique($handler(payload: $pont_thermique_payload, entity: $entity));
        }

        return $entity;
    }
}
