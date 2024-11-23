<?php

namespace App\Database\Local\Ecs;

use App\Domain\Ecs\Data\{Cop, CopRepository};
use App\Database\Local\{XMLTableElement, XMLTableRepositoryTrait};
use App\Domain\Common\Enum\ZoneClimatique;
use App\Domain\Ecs\Enum\{TypeGenerateur};

final class XMLCopRepository implements CopRepository
{
    use XMLTableRepositoryTrait;

    public static function table(): string
    {
        return 'ecs.cop';
    }

    public function find_by(
        ZoneClimatique $zone_climatique,
        TypeGenerateur $type_generateur,
        int $annee_installation,
    ): ?Cop {
        $record = $this->createQuery()
            ->and('zone_climatique', $zone_climatique->code())
            ->and('type_generateur', $type_generateur->id())
            ->andCompareTo('annee_installation', $annee_installation)
            ->getOne();
        return $record ? $this->to($record) : null;
    }

    protected function to(XMLTableElement $record): Cop
    {
        return new Cop(cop: (float) $record->cop,);
    }
}
