<?php

namespace App\Database\Opendata\Visite;

use App\Database\Opendata\Audit\XMLAuditTransformer;
use App\Database\Opendata\XMLElement;
use App\Domain\Common\Type\Id;
use App\Domain\Visite\Entity\Logement;
use App\Domain\Visite\Enum\Typologie;
use App\Domain\Visite\{Visite, VisiteFactory};

final class XMLVisiteTransformer
{
    public function __construct(
        private VisiteFactory $factory,
        private XMLAuditTransformer $audit_transformer,
    ) {}

    public function transform(XMLElement $root): Visite
    {
        $audit = $this->audit_transformer->transform($root);
        $reader = $root->read_visite();
        $visite = $this->factory->build($audit);

        foreach ($reader->read_logements() as $reader) {
            $logement = new Logement(
                id: $reader->id(),
                visite: $visite,
                description: $reader->description(),
                typologie: $reader->typologoie(),
                surface_habitable: $reader->surface_habitable(),
            );
            $visite->add_logement($logement);
        }

        if (0 === $visite->logements()->count()) {
            $surface_habitable = $visite->audit()->surface_habitable_moyenne();
            $logement = new Logement(
                id: Id::create(),
                visite: $visite,
                description: 'Logement visité reconstitué',
                typologie: Typologie::from_surface_habitable($surface_habitable),
                surface_habitable: $surface_habitable,
            );

            $visite->add_logement($logement);
        }

        return $visite;
    }
}
