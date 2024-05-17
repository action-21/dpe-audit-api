<?php

namespace App\Domain\Baie\Table;

use App\Domain\Baie\Enum\{NatureMenuiserie, TypeBaie};

interface UwRepository
{
    public function search(int $id): UwCollection;
    public function search_by(TypeBaie $type_baie, NatureMenuiserie $nature_menuiserie): UwCollection;
}
