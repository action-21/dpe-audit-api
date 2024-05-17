<?php

namespace App\Application\Photovoltaique;

use App\Application\Photovoltaique\View\PanneauPhotovoltaiqueView;
use App\Domain\Common\Enum\Mois;
use App\Domain\Photovoltaique\{InstallationPhotovoltaique, InstallationPhotovoltaiqueEngine};

class InstallationPhotovoltaiqueView
{
    public function __construct(
        public readonly float $surface_capteurs,
        /** @var array<PanneauPhotovoltaiqueView> */
        public readonly array $panneau_photovoltaique_collection,
        /** @var ?array<float> */
        public readonly ?array $ppv_j = null,
        public readonly ?float $ppv = null,
    ) {
    }

    public static function from_entity(InstallationPhotovoltaique $entity): self
    {
        return new self(
            surface_capteurs: $entity->surface_capteurs(),
            panneau_photovoltaique_collection: PanneauPhotovoltaiqueView::from_entity_collection($entity->panneau_photovoltaique_collection()),
        );
    }

    public static function from_engine(InstallationPhotovoltaiqueEngine $engine): self
    {
        $entity = $engine->input();
        return new self(
            surface_capteurs: $entity->surface_capteurs(),
            panneau_photovoltaique_collection: PanneauPhotovoltaiqueView::from_engine_collection($engine->panneau_photovoltaique_engine_collection()),
            ppv: $engine->ppv(),
            ppv_j: \array_map(fn (Mois $mois) => $engine->ppv_j($mois), Mois::cases()),
        );
    }
}
