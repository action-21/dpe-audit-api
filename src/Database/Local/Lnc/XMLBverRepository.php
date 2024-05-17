<?php

namespace App\Database\Local\Lnc;

use App\Database\Local\{XMLTableElement, XMLTableRepositoryTrait};
use App\Domain\Batiment\Enum\ZoneClimatique;
use App\Domain\Common\Enum\Orientation;
use App\Domain\Lnc\Table\{BVer, BVerCollection, BVerRepository};

final class XMLBverRepository implements BVerRepository
{
    use XMLTableRepositoryTrait;

    public static function table(): string
    {
        return 'lnc.bver.xml';
    }

    public function search(int $id): BVerCollection
    {
        return new BVerCollection(\array_map(
            fn (XMLTableElement $record): BVer => $this->to($record),
            $this->createQuery()->and(\sprintf('@id = "%s"', $id))->getMany(),
        ));
    }

    public function search_by(ZoneClimatique $zone_climatique, bool $isolation_aiu): BVerCollection
    {
        return new BVerCollection(\array_map(
            fn (XMLTableElement $record): Bver => $this->to($record),
            $this->createQuery()
                ->and(\sprintf('zone_climatique = "%s"', $zone_climatique->code()))
                ->and(\sprintf('isolation_aiu = "%s"', (int) $isolation_aiu))
                ->getMany()
        ));
    }

    public function to(XMLTableElement $record): BVer
    {
        return new BVer(
            id: $record->id(),
            orientation: Orientation::from((string) $record->orientation),
            bver: (float) $record->bver
        );
    }
}
