<?php

namespace App\Domain\Baie\Table;

use App\Domain\Baie\Enum\{NatureGazLame, TypeVitrage};

interface UgRepository
{
    public function search(int $id): UgCollection;
    public function search_by(TypeVitrage $type_vitrage, ?NatureGazLame $nature_gaz_lame, ?int $inclinaison_vitrage): UgCollection;
}
