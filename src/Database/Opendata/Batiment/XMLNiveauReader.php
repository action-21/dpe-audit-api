<?php

namespace App\Database\Opendata\Batiment;

use App\Database\Opendata\{XMLElement, XMLReaderIterator};
use App\Domain\Batiment\ValueObject\Hauteur;
use App\Domain\Batiment\ValueObject\SurfaceHabitable;

final class XMLNiveauReader extends XMLReaderIterator
{
    private XMLBatimentReader $reader;

    // Données déduites

    public function surface_habitable(): SurfaceHabitable
    {
        return SurfaceHabitable::from($this->reader->surface_habitable()->valeur() / $this->reader->nombre_niveau());
    }

    public function hauteur_sous_plafond(): Hauteur
    {
        return Hauteur::from($this->reader->hauteur_sous_plafond()->valeur() / $this->reader->nombre_niveau());
    }

    public function read(XMLBatimentReader $reader): self
    {
        $this->reader = $reader;
        $this->array = [];
        for ($i = 1; $i <= $reader->nombre_niveau(); $i++) {
            $this->array[] = $reader->xml();
        }
        return $this;
    }
}
