<?php

namespace App\Domain\Photovoltaique;

use App\Domain\Common\ValueObject\Id;

interface InstallationPhotovoltaiqueRepository
{
    public function find(Id $id): ?InstallationPhotovoltaique;
}
