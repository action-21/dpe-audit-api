<?php

namespace App\Api\Lnc\Payload;

use App\Domain\Lnc\Enum\{NatureMenuiserie, TypeVitrage};
use App\Domain\Lnc\ValueObject\Menuiserie;

final class MenuiseriePayload
{
    public function __construct(
        public NatureMenuiserie $nature_menuiserie,
        public TypeVitrage $type_vitrage,
        public ?bool $presence_rupteur_pont_thermique,
    ) {}

    public function to(): Menuiserie
    {
        return Menuiserie::create(
            nature_menuiserie: $this->nature_menuiserie,
            type_vitrage: $this->type_vitrage,
            presence_rupteur_pont_thermique: $this->presence_rupteur_pont_thermique,
        );
    }
}
