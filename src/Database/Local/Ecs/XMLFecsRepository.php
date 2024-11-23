<?php

namespace App\Database\Local\Ecs;

use App\Database\Local\{XMLTableElement, XMLTableRepositoryTrait};
use App\Domain\Common\Enum\{Enum, ZoneClimatique};
use App\Domain\Ecs\Data\{Fecs, FecsRepository};
use App\Domain\Ecs\Enum\{UsageEcs};

final class XMLFecsRepository implements FecsRepository
{
    use XMLTableRepositoryTrait;

    public static function table(): string
    {
        return 'ecs.fecs';
    }

    public function find_by(
        Enum $type_batiment,
        ZoneClimatique $zone_climatique,
        UsageEcs $usage_systeme_solaire,
        int $anciennete_installation,
    ): ?Fecs {
        $record = $this->createQuery()
            ->and('type_batiment', $type_batiment->id())
            ->and('zone_climatique', $zone_climatique->value)
            ->and('usage_systeme_solaire', $usage_systeme_solaire->id())
            ->andCompareTo('anciennete_installation', $anciennete_installation)
            ->getOne();
        return $record ? $this->to($record) : null;
    }

    protected function to(XMLTableElement $record): Fecs
    {
        return new Fecs(fecs: (float) $record->fecs,);
    }
}
