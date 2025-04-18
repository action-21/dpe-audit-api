<?php

namespace App\Database\Opendata\Production;

use App\Database\Opendata\XMLElement;
use App\Domain\Production\Data\PanneauPhotovoltaiqueData;
use App\Domain\Production\Entity\{PanneauPhotovoltaique};
use App\Domain\Production\Production;

final class XMLProductionDeserializer
{
    private XMLProductionReader $reader;
    private Production $entity;

    public function deserialize(XMLElement $xml): Production
    {
        $this->reader = XMLProductionReader::from($xml);
        $this->entity = Production::create();

        $this->deserialize_panneaux_photovoltaiques();

        return $this->entity;
    }

    private function deserialize_panneaux_photovoltaiques(): void
    {
        foreach ($this->reader->panneaux_photovoltaiques() as $reader) {
            $this->entity->add_panneau_photovoltaique(new PanneauPhotovoltaique(
                id: $reader->id(),
                production: $this->entity,
                description: $reader->description(),
                orientation: $reader->orientation(),
                inclinaison: $reader->inclinaison(),
                surface: $reader->surface_capteurs(),
                modules: $reader->modules(),
                data: PanneauPhotovoltaiqueData::create(),
            ));
        }
    }
}
