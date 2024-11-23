<?php

namespace App\Domain\Baie\Data;

use App\Domain\Baie\Enum\{NatureGazLame, TypeBaie, TypeSurvitrage, TypeVitrage};

interface UgRepository
{
    public function search_by(
        TypeBaie $type_baie,
        ?TypeVitrage $type_vitrage,
        ?TypeSurvitrage $type_survitrage,
        ?NatureGazLame $nature_gaz_lame,
        ?float $inclinaison_vitrage,
    ): UgCollection;
}
