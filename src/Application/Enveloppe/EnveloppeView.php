<?php

namespace App\Application\Enveloppe;

use App\Application\Baie\BaieView;
use App\Application\Enveloppe\View\{ApportView, DeperditionView, InertieView, PermeabiliteView};
use App\Application\Lnc\LncView;
use App\Application\MasqueLointain\MasqueLointainView;
use App\Application\MasqueProche\MasqueProcheView;
use App\Application\Mur\MurView;
use App\Application\PlancherBas\PlancherBasView;
use App\Application\PlancherHaut\PlancherHautView;
use App\Application\PlancherIntermediaire\PlancherIntermediaireView;
use App\Application\PontThermique\PontThermiqueView;
use App\Application\Porte\PorteView;
use App\Application\Refend\RefendView;
use App\Application\Ventilation\InstallationVentilationView;
use App\Domain\Common\Enum\Enum;
use App\Domain\Enveloppe\{Enveloppe, EnveloppeEngine};

class EnveloppeView
{
    public function __construct(
        public readonly Enum $exposition,
        public readonly ?float $q4pa_conv_saisi,
        /** @var array<BaieView> */
        public readonly array $baie_collection,
        /** @var array<LncView> */
        public readonly array $lnc_collection,
        /** @var array<MasqueProcheView> */
        public readonly array $masque_proche_collection,
        /** @var array<MasqueLointainView> */
        public readonly array $masque_lointain_collection,
        /** @var array<MurView> */
        public readonly array $mur_collection,
        /** @var array<PlancherBasView> */
        public readonly array $plancher_bas_collection,
        /** @var array<PlancherHautView> */
        public readonly array $plancher_haut_collection,
        /** @var array<PlancherIntermediaireView> */
        public readonly array $plancher_intermediaire_collection,
        /** @var array<PontThermiqueView> */
        public readonly array $pont_thermique_collection,
        /** @var array<PorteView> */
        public readonly array $porte_collection,
        /** @var array<RefendView> */
        public readonly array $refend_collection,
        /** @var array<InstallationVentilationView> */
        public readonly array $installation_ventilation_collection,
        public readonly ?InertieView $inertie = null,
        public readonly ?PermeabiliteView $permeabilite = null,
        public readonly ?DeperditionView $deperdition = null,
        public readonly ?ApportView $apport = null,
    ) {
    }

    public static function from_entity(Enveloppe $entity): self
    {
        return new self(
            exposition: $entity->permeabilite()->exposition,
            q4pa_conv_saisi: $entity->permeabilite()->q4pa_conv?->valeur(),
            baie_collection: BaieView::from_entity_collection($entity->baie_collection()),
            lnc_collection: LncView::from_entity_collection($entity->lnc_collection()),
            masque_proche_collection: MasqueProcheView::from_entity_collection($entity->masque_proche_collection()),
            masque_lointain_collection: MasqueLointainView::from_entity_collection($entity->masque_lointain_collection()),
            mur_collection: MurView::from_entity_collection($entity->mur_collection()),
            plancher_bas_collection: PlancherBasView::from_entity_collection($entity->plancher_bas_collection()),
            plancher_intermediaire_collection: PlancherIntermediaireView::from_entity_collection($entity->plancher_intermediaire_collection()),
            plancher_haut_collection: PlancherHautView::from_entity_collection($entity->plancher_haut_collection()),
            pont_thermique_collection: PontThermiqueView::from_entity_collection($entity->pont_thermique_collection()),
            porte_collection: PorteView::from_entity_collection($entity->porte_collection()),
            refend_collection: RefendView::from_entity_collection($entity->refend_collection()),
            installation_ventilation_collection: InstallationVentilationView::from_entity_collection($entity->batiment()->installation_ventilation_collection()),
        );
    }

    public static function from_engine(EnveloppeEngine $engine): self
    {
        $entity = $engine->input();
        return new self(
            exposition: $entity->permeabilite()->exposition,
            q4pa_conv_saisi: $entity->permeabilite()->q4pa_conv?->valeur(),
            baie_collection: BaieView::from_engine_collection($engine->baie_engine_collection()),
            lnc_collection: LncView::from_engine_collection($engine->lnc_engine_collection()),
            masque_proche_collection: MasqueProcheView::from_engine_collection($engine->masque_proche_engine_collection()),
            masque_lointain_collection: MasqueLointainView::from_engine_collection($engine->masque_lointain_engine_collection()),
            mur_collection: MurView::from_engine_collection($engine->mur_engine_collection()),
            plancher_bas_collection: PlancherBasView::from_engine_collection($engine->plancher_bas_engine_collection()),
            plancher_intermediaire_collection: PlancherIntermediaireView::from_entity_collection($entity->plancher_intermediaire_collection()),
            plancher_haut_collection: PlancherHautView::from_engine_collection($engine->plancher_haut_engine_collection()),
            pont_thermique_collection: PontThermiqueView::from_engine_collection($engine->pont_thermique_engine_collection()),
            porte_collection: PorteView::from_engine_collection($engine->porte_engine_collection()),
            refend_collection: RefendView::from_entity_collection($entity->refend_collection()),
            installation_ventilation_collection: InstallationVentilationView::from_engine_collection($engine->installation_ventilation_engine_collection()),
            inertie: InertieView::from_engine($engine),
            permeabilite: PermeabiliteView::from_engine($engine),
            deperdition: DeperditionView::from_engine($engine),
            apport: ApportView::from_engine($engine),
        );
    }
}
