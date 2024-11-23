<?php

namespace App\Domain\Audit\ValueObject;

use App\Domain\Common\Enum\ZoneClimatique;

final class Adresse
{
    public function __construct(
        public readonly string $libelle,
        public readonly string $code_postal,
        public readonly string $commune,
        public readonly ?string $ban_id,
        public readonly ?string $rnb_id,
        public readonly string $code_departement,
        public readonly ZoneClimatique $zone_climatique,
    ) {}

    public static function create(
        string $libelle,
        string $code_postal,
        string $commune,
        ?string $ban_id,
        ?string $rnb_id,
    ): self {
        return new self(
            libelle: $libelle,
            code_postal: $code_postal,
            commune: $commune,
            ban_id: $ban_id,
            rnb_id: $rnb_id,
            code_departement: ($code_departement = \substr($code_postal, 0, 2)),
            zone_climatique: ZoneClimatique::from_code_departement($code_departement),
        );
    }
}
