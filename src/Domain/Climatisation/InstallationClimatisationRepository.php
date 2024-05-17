<?php

namespace App\Domain\Climatisation;

use App\Domain\Common\ValueObject\Id;

interface InstallationClimatisationRepository
{
    public function find(Id $id): ?InstallationClimatisation;
    public function search(Id $batiment_id): InstallationClimatisationCollection;
}
