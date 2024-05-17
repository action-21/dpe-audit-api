<?php

namespace App\Domain\Chauffage\Engine;

use App\Domain\Chauffage\Entity\Generateur;
use App\Domain\Chauffage\InstallationChauffageEngine;

final class GenerateurEngine
{
    private Generateur $input;
    private InstallationChauffageEngine $engine;

    public function input(): Generateur
    {
        return $this->input;
    }

    public function engine(): InstallationChauffageEngine
    {
        return $this->engine;
    }
}
