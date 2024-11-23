<?php

namespace App\Domain\Ecs\ValueObject;

use App\Domain\Common\Service\Assert;

final class Stockage
{
    public function __construct(
        public readonly int $volume_stockage,
        public readonly bool $position_volume_chauffe,
    ) {}

    public static function create(int $volume_stockage, bool $position_volume_chauffe): self
    {
        return new self(
            volume_stockage: $volume_stockage,
            position_volume_chauffe: $position_volume_chauffe,
        );
    }

    public function controle(): void
    {
        Assert::positif($this->volume_stockage);
    }
}
