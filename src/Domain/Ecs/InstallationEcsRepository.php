<?php

namespace App\Domain\Ecs;

use App\Domain\Common\ValueObject\Id;

interface InstallationEcsRepository
{
    public function find(Id $id): ?InstallationEcs;
    public function search(Id $logement_id): InstallationEcsCollection;
}
