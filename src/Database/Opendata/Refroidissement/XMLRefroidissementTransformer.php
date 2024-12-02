<?php

namespace App\Database\Opendata\Refroidissement;

use App\Database\Opendata\Audit\XMLAuditTransformer;
use App\Database\Opendata\XMLElement;
use App\Domain\Common\Type\Id;
use App\Domain\Refroidissement\Entity\{Generateur, Installation, Systeme, SystemeCollection};
use App\Domain\Refroidissement\{Refroidissement, RefroidissementFactory};

final class XMLRefroidissementTransformer
{
    public function __construct(
        private RefroidissementFactory $factory,
        private XMLAuditTransformer $audit_transformer,
    ) {}

    public function transform(XMLElement $root): Refroidissement
    {
        $audit = $this->audit_transformer->transform($root);
        $refroidissement = $this->factory->build($audit);

        $this->set_generateurs($root, $refroidissement);
        $this->set_installations($root, $refroidissement);
        return $refroidissement;
    }

    private function set_generateurs(XMLElement $root, Refroidissement $refroidissement): void
    {
        foreach ($root->read_refroidissement()->read_climatisations() as $reader) {
            $generateur = new Generateur(
                id: $reader->id(),
                refroidissement: $refroidissement,
                description: $reader->description(),
                type_generateur: $reader->type_generateur(),
                energie_generateur: $reader->energie_generateur(),
                annee_installation: $reader->annee_installation(),
                seer: null,
            );
            $refroidissement->add_generateur($generateur);
        }
    }

    private function set_installations(XMLElement $root, Refroidissement $refroidissement): void
    {
        foreach ($root->read_refroidissement()->read_climatisations() as $reader) {
            if (null === $generateur = $refroidissement->generateurs()->find(id: $reader->id()))
                throw new \RuntimeException("Le générateur {$reader->id()} n'a pas été trouvé.");

            $installation = new Installation(
                id: $reader->id(),
                refroidissement: $refroidissement,
                description: $reader->description(),
                surface: $reader->surface(),
                systemes: new SystemeCollection(),
            );
            $systeme = new Systeme(
                id: Id::create(),
                installation: $installation,
                generateur: $generateur,
            );
            $installation->add_systeme($systeme);
            $refroidissement->add_installation($installation);
        }
    }
}
