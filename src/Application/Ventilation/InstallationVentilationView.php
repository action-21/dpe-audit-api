<?php

namespace App\Application\Ventilation;

use App\Application\Ventilation\View\VentilationView;
use App\Domain\Common\Enum\Mois;
use App\Domain\Ventilation\{InstallationVentilation, InstallationVentilationCollection};
use App\Domain\Ventilation\{InstallationVentilationEngine, InstallationVentilationEngineCollection};

class InstallationVentilationView
{
    public function __construct(
        public readonly float $surface_ventilee,
        /** @var array<VentilationView> */
        public readonly array $ventilation,
        public readonly ?float $caux = null,
        /** @var ?array<float> */
        public readonly ?array $caux_j = null,
        public readonly ?float $qvarep_conv = null,
        public readonly ?float $qvasouf_conv = null,
        public readonly ?float $smea_conv = null,
        public readonly ?float $rdim = null,
    ) {
    }

    public static function from_entity(InstallationVentilation $entity): self
    {
        return new self(
            surface_ventilee: $entity->surface(),
            ventilation: VentilationView::from_entity_collection($entity->ventilation_collection()),
        );
    }

    /** @return self[] */
    public static function from_entity_collection(InstallationVentilationCollection $collection): array
    {
        return \array_map(fn (InstallationVentilation $entity) => self::from_entity($entity), $collection->to_array());
    }

    public static function from_engine(InstallationVentilationEngine $engine): self
    {
        $entity = $engine->input();
        return new self(
            surface_ventilee: $entity->surface(),
            ventilation: VentilationView::from_engine_collection($engine->ventilation_engine_collection()),
            rdim: $engine->rdim(),
            caux: $engine->caux(),
            caux_j: \array_map(fn (Mois $mois): float => $engine->caux_j($mois), Mois::cases()),
            qvarep_conv: $engine->qvarep_conv(),
            qvasouf_conv: $engine->qvasouf_conv(),
            smea_conv: $engine->smea_conv(),
        );
    }

    /** @return self[] */
    public static function from_engine_collection(InstallationVentilationEngineCollection $collection): array
    {
        return \array_map(fn (InstallationVentilationEngine $engine) => self::from_engine($engine), $collection->liste());
    }
}
