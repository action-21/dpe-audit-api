<?php

namespace App\Database\Local\Eclairage;

use App\Domain\Eclairage\Data\{Nhecl, NheclCollection, NheclRepository};
use App\Database\Local\{XMLTableElement, XMLTableRepositoryTrait};
use App\Domain\Common\Enum\{Mois, ZoneClimatique};

final class XMLEclairageRepository implements NheclRepository
{
    use XMLTableRepositoryTrait;

    public static function table(): string
    {
        return 'eclairage.nhecl';
    }

    public function search_by(ZoneClimatique $zone_climatique): NheclCollection
    {
        return new NheclCollection(\array_map(
            fn(XMLTableElement $record) => $this->to($record),
            $this->createQuery()
                ->and('zone_climatique', $zone_climatique->value)
                ->getMany()
        ));
    }

    protected function to(XMLTableElement $record): Nhecl
    {
        return new Nhecl(
            mois: Mois::from($record->get('mois')->strval()),
            nhecl: $record->get('nhecl')->floatval(),
        );
    }
}
