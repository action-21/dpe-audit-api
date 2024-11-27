<?php

namespace App\Database\Local\Lnc;

use App\Database\Local\{XMLTableElement, XMLTableRepositoryTrait};
use App\Domain\Common\Enum\{Orientation, ZoneClimatique};
use App\Domain\Lnc\Data\{BVer, BverCollection, BVerRepository};

final class XMLBverRepository implements BVerRepository
{
    use XMLTableRepositoryTrait;

    public static function table(): string
    {
        return 'lnc.bver';
    }

    public function search_by(ZoneClimatique $zone_climatique): BverCollection
    {
        return new BverCollection(\array_map(
            fn(XMLTableElement $record): Bver => $this->to($record),
            $this->createQuery()
                ->and('zone_climatique', $zone_climatique->code())
                ->getMany()
        ));
    }

    public function to(XMLTableElement $record): BVer
    {
        return new BVer(
            orientation: Orientation::from($record->get('orientation')->strval()),
            isolation_paroi: $record->get('isolation_paroi')->boolval(),
            bver: $record->get('bver')->floatval(),
        );
    }
}
