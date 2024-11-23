<?php

namespace App\Database\Local\Baie;

use App\Database\Local\{XMLTableElement, XMLTableRepositoryTrait};
use App\Domain\Common\Enum\{Mois, Orientation, ZoneClimatique};
use App\Domain\Baie\Data\{C1, C1Collection, C1Repository};

final class XMLC1Repository implements C1Repository
{
    use XMLTableRepositoryTrait;

    public static function table(): string
    {
        return 'common.c1';
    }

    public function search_by(
        ZoneClimatique $zone_climatique,
        float $inclinaison,
        ?Orientation $orientation,
    ): C1Collection {
        return new C1Collection(\array_map(
            fn(XMLTableElement $record): C1 => $this->to($record),
            $this->createQuery()
                ->and('zone_climatique', $zone_climatique->value)
                ->and('orientation', $orientation?->id(), true)
                ->andCompareTo('inclinaison', $inclinaison)
                ->getMany()
        ));
    }

    protected function to(XMLTableElement $record): C1
    {
        return new C1(
            mois: Mois::from($record->get('mois')->strval()),
            c1: $record->get('c1')->floatval(),
        );
    }
}
