<?php

namespace App\Domain\Porte\Table;

use App\Domain\Porte\Enum\{NatureMenuiserie, TypePorte};

interface UporteRepository
{
    public function find(int $id): ?Uporte;
    public function find_by(NatureMenuiserie $nature_menuiserie, TypePorte $type_porte): ?Uporte;
}
