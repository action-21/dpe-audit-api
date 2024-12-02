<?php

namespace App\Database\Opendata\Production;

use App\Database\Opendata\Audit\XMLAuditTransformer;
use App\Database\Opendata\XMLElement;
use App\Domain\Production\Entity\{PanneauPhotovoltaique};
use App\Domain\Production\{Production, ProductionFactory};

final class XMLProductionTransformer
{
    public function __construct(
        private ProductionFactory $factory,
        private XMLAuditTransformer $audit_transformer,
    ) {}

    public function transform(XMLElement $root): Production
    {
        $audit = $this->audit_transformer->transform($root);
        $production = $this->factory->build($audit);

        $this->set_panneaux_photovoltaiques($root, $production);
        return $production;
    }

    private function set_panneaux_photovoltaiques(XMLElement $root, Production $production): void
    {
        foreach ($root->read_production()->read_panneaux_photovoltaiques() as $reader) {
            if (false === $reader->apply())
                continue;

            $panneau_pv = new PanneauPhotovoltaique(
                id: $reader->id(),
                production: $production,
                orientation: $reader->orientation(),
                inclinaison: $reader->inclinaison(),
                surface_capteurs: $reader->surface_capteurs(),
                modules: $reader->modules(),
            );
            $production->add_panneau_photovoltaique($panneau_pv);
        }
    }
}
