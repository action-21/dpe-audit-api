<?php

namespace App\Domain\Ecs\ValueObject;

use Webmozart\Assert\Assert;

final class Stockage
{
    public function __construct(
        public readonly int $volume,
        public readonly bool $position_volume_chauffe,
    ) {}

    public static function create(int $volume, bool $position_volume_chauffe): self
    {
        Assert::greaterThan($volume, 0);
        return new self(
            volume: $volume,
            position_volume_chauffe: $position_volume_chauffe,
        );
    }
}
