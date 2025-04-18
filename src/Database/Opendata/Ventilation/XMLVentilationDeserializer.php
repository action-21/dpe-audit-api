<?php

namespace App\Database\Opendata\Ventilation;

use App\Database\Opendata\XMLElement;
use App\Domain\Ventilation\Data\{GenerateurData, InstallationData, SystemeData};
use App\Domain\Ventilation\Entity\{Generateur, Installation, Systeme};
use App\Domain\Ventilation\Ventilation;
use Webmozart\Assert\Assert;

final class XMLVentilationDeserializer
{
    private XMLVentilationReader $reader;
    private Ventilation $entity;

    public function deserialize(XMLElement $xml): Ventilation
    {
        $this->reader = XMLVentilationReader::from($xml);
        $this->entity = Ventilation::create();

        $this->deserialize_generateurs();
        $this->deserialize_installations();
        $this->deserialize_systemes();

        return $this->entity;
    }

    private function deserialize_generateurs(): void
    {
        foreach ($this->reader->generateurs() as $reader) {
            $this->entity->add_generateur(new Generateur(
                id: $reader->id(),
                ventilation: $this->entity,
                description: $reader->description(),
                type: $reader->type_generateur(),
                type_vmc: $reader->type_vmc(),
                presence_echangeur_thermique: $reader->presence_echangeur_thermique(),
                generateur_collectif: $reader->generateur_collectif(),
                annee_installation: $reader->annee_installation(),
                data: GenerateurData::create(),
            ));
        }
    }

    private function deserialize_installations(): void
    {
        foreach ($this->reader->installations() as $reader) {
            $this->entity->add_installation(new Installation(
                id: $reader->id(),
                ventilation: $this->entity,
                description: $reader->description(),
                surface: $reader->surface(),
                data: InstallationData::create(),
            ));
        }
    }

    private function deserialize_systemes(): void
    {
        foreach ($this->reader->systemes() as $reader) {
            $installation = $this->entity->installations()->find($reader->installation_id());
            Assert::notNull($installation, "L'installation {$reader->installation_id()} n'existe pas");

            $generateur = null;
            if ($reader->generateur_id()) {
                $generateur = $this->entity->generateurs()->find($reader->generateur_id());
                Assert::notNull($generateur, "Le générateur {$reader->generateur_id()} n'existe pas");
            }
            $this->entity->add_systeme(new Systeme(
                id: $reader->id(),
                ventilation: $this->entity,
                installation: $installation,
                type: $reader->type_ventilation(),
                generateur: $generateur,
                data: SystemeData::create(),
            ));
        }
    }
}
