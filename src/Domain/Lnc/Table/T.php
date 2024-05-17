<?php

namespace App\Domain\Lnc\Table;

use App\Domain\Common\Table\TableValue;
use App\Domain\Lnc\Enum\{NatureMenuiserie, TypeVitrage};

class T implements TableValue
{
    public function __construct(
        public readonly int $id,
        public readonly NatureMenuiserie $nature_menuiserie,
        public readonly ?TypeVitrage $type_vitrage,
        public readonly int $tv_coef_transparence_ets_id,
        public readonly float $t,
    ) {
    }

    public function id(): int
    {
        return $this->id;
    }

    public function valeur(): float
    {
        return $this->t;
    }
}
