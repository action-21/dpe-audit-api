<?php

namespace App\Application\Batiment\View;

use App\Domain\Batiment\ValueObject\Adresse;
use App\Domain\Common\Enum\Enum;

class AdresseView
{
    public function __construct(
        public readonly string $label,
        public readonly string $code_postal,
        public readonly string $commune,
        public readonly ?string $ban_id,
        public readonly ?string $rnb_id,
        public readonly Enum $zone_climatique,
    ) {
    }

    public static function from_vo(Adresse $vo): self
    {
        return new self(
            label: $vo->label,
            code_postal: $vo->code_postal,
            commune: $vo->commune,
            ban_id: $vo->ban_id,
            rnb_id: $vo->rnb_id,
            zone_climatique: $vo->zone_climatique,
        );
    }
}
