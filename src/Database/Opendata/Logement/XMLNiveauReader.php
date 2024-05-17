<?php

namespace App\Database\Opendata\Logement;

use App\Database\Opendata\{XMLElement, XMLReaderIterator};
use App\Domain\Common\Identifier\Uuid;
use App\Domain\Logement\ValueObject\HauteurSousPlafond;
use App\Domain\Logement\ValueObject\SurfaceHabitable;

final class XMLNiveauReader extends XMLReaderIterator
{
    private XMLLogementReader $context;

    // Données déduites

    public function id(): \Stringable
    {
        return Uuid::create();
    }

    public function description(): string
    {
        return 'Niveau non décrit';
    }

    public function surface(): SurfaceHabitable
    {
        return SurfaceHabitable::from($this->context->surface_habitable());
    }

    public function hauteur_sous_plafond(): HauteurSousPlafond
    {
        return HauteurSousPlafond::from($this->context->hsp());
    }

    public function read(XMLElement $xml, XMLLogementReader $context): self
    {
        $this->array = [$xml];
        $this->context = $context;
        return $this;
    }
}
