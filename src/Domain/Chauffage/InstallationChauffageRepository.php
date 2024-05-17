<?php

namespace App\Domain\Chauffage;

use App\Domain\Common\ValueObject\Id;

interface InstallationChauffageRepository
{
    public function find(Id $id): ?InstallationChauffage;
    public function search(Id $audit_id): InstallationChauffageCollection;
}
