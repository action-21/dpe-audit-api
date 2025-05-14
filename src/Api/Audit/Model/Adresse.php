<?php

namespace App\Api\Audit\Model;

use App\Domain\Audit\Audit as Entity;
use App\Services\Validator\Constraints as DpeAssert;
use Symfony\Component\Validator\Constraints as Assert;

final class Adresse
{
    public function __construct(
        public ?string $numero,

        public string $nom,

        public string $code_postal,

        public string $code_commune,

        public string $commune,

        public ?string $ban_id,
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
