<?php

namespace App\Domain\Audit\ValueObject;

final class Adresse
{
    public function __construct(
        public readonly ?string $numero,
        public readonly string $nom,
        public readonly string $code_departement,
        public readonly string $code_postal,
        public readonly string $code_commune,
        public readonly string $commune,
        public readonly ?string $ban_id,
    ) {}

    public static function create(
        ?string $numero,
        string $nom,
        string $code_postal,
        string $code_commune,
        string $commune,
        ?string $ban_id,
    ): self {
        return new self(
            numero: $numero,
            nom: $nom,
            code_postal: $code_postal,
            code_commune: $code_commune,
            commune: $commune,
            ban_id: $ban_id,
            code_departement: \substr($code_commune, 0, 2),
        );
    }
}
