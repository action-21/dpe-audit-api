<?php

namespace App\Api\Eclairage\Model;

use App\Domain\Eclairage\Eclairage as Entity;

final class Eclairage
{
    public function __construct(
        public readonly ?EclairageData $data,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            data: EclairageData::from($entity),
        );
    }
}
