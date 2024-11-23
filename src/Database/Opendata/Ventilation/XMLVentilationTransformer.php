<?php

namespace App\Database\Opendata\Ventilation;

use App\Database\Opendata\Audit\XMLAuditTransformer;
use App\Database\Opendata\XMLElement;
use App\Domain\Ventilation\Entity\{Generateur, Installation, Systeme, SystemeCollection};
use App\Domain\Ventilation\{Ventilation, VentilationFactory};

final class XMLVentilationTransformer
{
    public function __construct(
        private VentilationFactory $factory,
        private XMLAuditTransformer $audit_transformer,
        private XMLVentilationReader $reader,
    ) {}

    public function transform(XMLElement $root): Ventilation
    {
        $audit = $this->audit_transformer->transform($root);
        $ventilation = $this->factory->build($audit);

        $this->set_generateurs($root, $ventilation);
        $this->set_installations($root, $ventilation);
        return $ventilation;
    }

    private function set_generateurs(XMLElement $root, Ventilation $ventilation): void
    {
        foreach ($this->reader->read($root->ventilation_collection()) as $reader) {
            if (null === $type_generateur = $reader->type_generateur())
                continue;

            $entity = new Generateur(
                id: $reader->id(),
                ventilation: $ventilation,
                description: $reader->description(),
                type_ventilation: $reader->type_ventilation(),
                type: $type_generateur,
                presence_echangeur_thermique: $reader->presence_echangeur_thermique(),
                generateur_collectif: $reader->generateur_collectif(),
                annee_installation: $reader->annee_installation(),
            );
            $ventilation->add_generateur($entity);
        }
    }

    private function set_installations(XMLElement $root, Ventilation $ventilation): void
    {
        foreach ($this->reader->read($root->ventilation_collection()) as $reader) {
            $entity = new Installation(
                id: $reader->id(),
                ventilation: $ventilation,
                surface: $reader->surface(),
                systemes: new SystemeCollection,
            );
            $ventilation->add_installation($entity);

            $generateur = null;
            if ($reader->type_generateur() && null === $generateur = $ventilation->generateurs()->find($reader->id()))
                throw new \Exception("Le générateur {$reader->id()} n'existe pas");

            $systeme = new Systeme(
                id: $reader->id(),
                installation: $entity,
                type: $reader->type_systeme(),
                generateur: $generateur,
                mode_extraction: $reader->mode_extraction(),
                mode_insufflation: $reader->mode_insufflation(),
            );
            $entity->add_systeme($systeme);
        }
    }
}
