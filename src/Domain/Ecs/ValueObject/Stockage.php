<?php

namespace App\Domain\Ecs\ValueObject;

use App\Domain\Ecs\Enum\TypeStockage;

final class Stockage
{
    public function __construct(
        public readonly TypeStockage $type_stockage,
        public readonly ?bool $position_volume_chauffe,
        public readonly ?VolumeStockage $volume_stockage,
    ) {
    }

    public static function create_sans_stockage(): self
    {
        return new static(
            type_stockage: TypeStockage::SANS_STOCKAGE,
            position_volume_chauffe: null,
            volume_stockage: null
        );
    }

    public static function create(
        TypeStockage $type_stockage,
        VolumeStockage $volume_stockage,
        bool $position_volume_chauffe,
    ): static {
        if ($type_stockage === TypeStockage::SANS_STOCKAGE) {
            return self::create_sans_stockage();
        }
        return new static(
            position_volume_chauffe: $position_volume_chauffe,
            volume_stockage: $volume_stockage,
            type_stockage: $type_stockage,
        );
    }
}
