<?php

namespace App\Api\Enveloppe\Model\Baie;

use App\Domain\Enveloppe\Entity\Baie\MasqueProche as Entity;

final class MasqueProcheData
{
    public function __construct(public ?float $fe1) {}

    public static function from(Entity $entity): self
    {
        return new self(fe1: $entity->data()->fe1);
    }
}
