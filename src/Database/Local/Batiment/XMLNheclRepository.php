<?php

namespace App\Database\Local\Batiment;

use App\Database\Local\{XMLTableElement, XMLTableRepositoryTrait};
use App\Domain\Batiment\Enum\ZoneClimatique;
use App\Domain\Batiment\Table\{Nhecl, NheclCollection, NheclRepository};
use App\Domain\Common\Enum\Mois;

final class XMLNheclRepository implements NheclRepository
{
    use XMLTableRepositoryTrait;

    public static function table(): string
    {
        return 'batiment.nhecl.xml';
    }

    public function search(int $id): NheclCollection
    {
        return new NheclCollection(\array_map(
            fn (XMLTableElement $record): Nhecl => $this->to($record),
            $this->createQuery()->and(\sprintf('@id = "%s"', $id))->getMany(),
        ));
    }

    public function search_by(ZoneClimatique $zone_climatique): NheclCollection
    {
        return new NheclCollection(\array_map(
            fn (XMLTableElement $record): Nhecl => $this->to($record),
            $this->createQuery()->and(\sprintf('zone_climatique = "%s"', $zone_climatique->lib()))->getMany(),
        ));
    }

    protected function to(XMLTableElement $record): Nhecl
    {
        return new Nhecl(
            id: $record->id(),
            mois: Mois::from_iso((string) $record->mois),
            nhecl: (float) $record->nhecl,
        );
    }
}
