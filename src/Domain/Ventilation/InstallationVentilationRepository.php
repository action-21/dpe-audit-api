<?php

namespace App\Domain\Ventilation;

use App\Domain\Common\ValueObject\Id;

interface InstallationVentilationRepository
{
    public function find(Id $id): ?InstallationVentilation;
    public function search(Id $batiment_id): InstallationVentilationCollection;
}
