<?php

namespace App\Domain\Batiment\ValueObject;

use App\Domain\Batiment\Enum\ZoneClimatique;

final class Adresse
{
    public readonly ZoneClimatique $zone_climatique;

    public function __construct(
        public readonly string $label,
        public readonly string $code_postal,
        public readonly string $commune,
        public readonly ?string $ban_id,
        public readonly ?string $rnb_id,
    ) {
        $this->zone_climatique = ZoneClimatique::from_code_departement($this->code_departement());
    }

    public function code_departement(): string
    {
        return \substr($this->code_postal, 0, 2);
    }
}
