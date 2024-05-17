<?php

namespace App\Database\Local\Baie;

use App\Database\Local\{XMLTableElement, XMLTableRepositoryTrait};
use App\Domain\Batiment\Enum\ZoneClimatique;
use App\Domain\Common\Enum\{Mois, Orientation};
use App\Domain\Baie\Table\{C1, C1Repository, C1Collection};

final class XMLC1Repository implements C1Repository
{
    use XMLTableRepositoryTrait;

    public static function table(): string
    {
        return 'baie.c1.xml';
    }

    public function search(int $id): C1Collection
    {
        return new C1Collection(\array_map(
            fn (XMLTableElement $record): C1 => $this->to($record),
            $this->createQuery()->and(\sprintf('@id = "%s"', $id))->getMany(),
        ));
    }

    public function search_by(ZoneClimatique $zone_climatique, int $inclinaison, ?Orientation $orientation): C1Collection
    {
        return new C1Collection(\array_map(
            fn (XMLTableElement $record): C1 => $this->to($record),
            $this->createQuery()
                ->and(\sprintf('zone_climatique = "%s"', $zone_climatique->lib()))
                ->and(\sprintf('orientation = "%s" or orientation = ""', $orientation->code()))
                ->andCompareTo('inclinaison', $inclinaison)
                ->getMany()
        ));
    }

    protected function to(XMLTableElement $record): C1
    {
        return new C1(
            id: $record->id(),
            mois: Mois::from_iso((string) $record->mois),
            c1: (float) $record->c1,
        );
    }
}
