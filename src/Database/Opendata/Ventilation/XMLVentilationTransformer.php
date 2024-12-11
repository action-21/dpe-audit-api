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
        foreach ($root->read_ventilation()->read_generateurs() as $reader) {
            $entity = new Generateur(
                id: $reader->id(),
                ventilation: $ventilation,
                description: $reader->description(),
                signaletique: $reader->signaletique(),
                generateur_collectif: $reader->generateur_collectif(),
                annee_installation: $reader->annee_installation(),
            );
            $ventilation->add_generateur($entity);
        }
    }

    private function set_installations(XMLElement $root, Ventilation $ventilation): void
    {
        foreach ($root->read_ventilation()->read_installations() as $reader) {
            $installation = new Installation(
                id: $reader->id(),
                ventilation: $ventilation,
                surface: $reader->surface(),
                systemes: new SystemeCollection,
            );
            $ventilation->add_installation($installation);

            foreach ($reader->read_systemes() as $systeme_reader) {
                $generateur = null;

                if ($systeme_reader->generateur_id() && null === $generateur = $ventilation->generateurs()->find($systeme_reader->generateur_id()))
                    throw new \Exception("Le générateur {$reader->id()} n'existe pas");

                $systeme = new Systeme(
                    id: $reader->id(),
                    installation: $installation,
                    type_ventilation: $systeme_reader->type_ventilation(),
                    generateur: $generateur,
                );
                $installation->add_systeme($systeme);
            }
        }
    }
}
