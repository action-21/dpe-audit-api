<?php

namespace App\Database\Opendata\Refroidissement;

use App\Database\Opendata\XMLElement;
use App\Domain\Refroidissement\Data\{GenerateurData, InstallationData, SystemeData};
use App\Domain\Refroidissement\Entity\{Generateur, Installation, Systeme};
use App\Domain\Refroidissement\Refroidissement;
use Webmozart\Assert\Assert;

final class XMLRefroidissementDeserializer
{
    private XMLRefroidissementReader $reader;
    private Refroidissement $entity;

    public function deserialize(XMLElement $xml): Refroidissement
    {
        $this->reader = XMLRefroidissementReader::from($xml);
        $this->entity = Refroidissement::create();

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
                refroidissement: $this->entity,
                reseau_froid: null,
                description: $reader->description(),
                type: $reader->type_generateur(),
                energie: $reader->energie_generateur(),
                annee_installation: $reader->annee_installation(),
                seer: $reader->eer(),
                data: GenerateurData::create(),
            ));
        }
    }

    private function deserialize_installations(): void
    {
        foreach ($this->reader->installations() as $reader) {
            $this->entity->add_installation(new Installation(
                id: $reader->id(),
                refroidissement: $this->entity,
                description: $reader->description(),
                surface: $reader->surface(),
                data: InstallationData::create(),
            ));
        }
    }

    private function deserialize_systemes(): void
    {
        foreach ($this->reader->systemes() as $reader) {
            $installation = $this->entity->installations()->find(id: $reader->installation_id());
            $generateur = $this->entity->generateurs()->find(id: $reader->generateur_id());

            Assert::notNull($installation, "L'installation {$reader->installation_id()} n'a pas été trouvée.");
            Assert::notNull($generateur, "Le générateur {$reader->generateur_id()} n'a pas été trouvé.");

            $this->entity->add_systeme(new Systeme(
                id: $reader->id(),
                refroidissement: $this->entity,
                installation: $installation,
                generateur: $generateur,
                data: SystemeData::create(),
            ));
        }
    }
}
