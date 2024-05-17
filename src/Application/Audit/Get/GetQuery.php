<?php

namespace App\Application\Audit\Get;

use App\Domain\Common\Identifier\Reference;

final class GetQuery
{
    public function __construct(public readonly string $id)
    {
    }

    public function toReference(): Reference
    {
        return Reference::create($this->id);
    }
}
