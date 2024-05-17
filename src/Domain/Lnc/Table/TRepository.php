<?php

namespace App\Domain\Lnc\Table;

use App\Domain\Lnc\Enum\{NatureMenuiserie, TypeVitrage};

interface TRepository
{
    public function find(int $id): ?T;
    public function find_by(NatureMenuiserie $nature_menuiserie, ?TypeVitrage $type_vitrage): ?T;

    public function search_by(
        ?NatureMenuiserie $nature_menuiserie = null,
        ?TypeVitrage $type_vitrage = null,
        ?int $tv_coef_transparence_ets_id = null,
    ): TCollection;
}
