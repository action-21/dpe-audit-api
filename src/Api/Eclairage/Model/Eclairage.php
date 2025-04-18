<?php

namespace App\Api\Eclairage\Model;

use App\Domain\Eclairage\Eclairage as Entity;

final class Eclairage
{
    public function __construct() {}

    public static function from(Entity $entity): self
    {
        return new self();
    }
}
