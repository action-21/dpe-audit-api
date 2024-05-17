<?php

namespace App\Domain\Ecs\Entity;

use App\Domain\Common\Collection\ArrayCollection;
use App\Domain\Common\ValueObject\Id;

/**
 * @var Generateur[] $elements
 */
final class GenerateurCollection extends ArrayCollection
{
    public function find(Id $id): ?Generateur
    {
        return $this->filter(fn (Generateur $generateur) => $generateur->id()->compare($id))->first();
    }
}
