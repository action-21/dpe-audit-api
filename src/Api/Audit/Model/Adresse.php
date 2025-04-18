<?php

namespace App\Api\Audit\Model;

use App\Domain\Audit\Audit as Entity;
use App\Services\Validator\Constraints as DpeAssert;
use Symfony\Component\Validator\Constraints as Assert;

final class Adresse
{
    public function __construct(
        public readonly ?string $numero,

        public readonly string $nom,

        public readonly string $code_postal,

        public readonly string $code_commune,

        public readonly string $commune,

        public readonly ?string $ban_id,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            numero: $entity->adresse()->numero,
            nom: $entity->adresse()->nom,
            code_postal: $entity->adresse()->code_postal,
            code_commune: $entity->adresse()->code_commune,
            commune: $entity->adresse()->commune,
            ban_id: $entity->adresse()->ban_id,
        );
    }
}
