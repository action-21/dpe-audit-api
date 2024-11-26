<?php

namespace App\Database\Local\Eclairage;

use App\Domain\Eclairage\Data\{Nhj, NhjCollection, NhjRepository};
use App\Database\Local\{XMLTableElement, XMLTableRepositoryTrait};
use App\Domain\Common\Enum\{Mois, ZoneClimatique};

final class XMLNhjRepository implements NhjRepository
{
    use XMLTableRepositoryTrait;

    public static function table(): string
    {
        return 'eclairage.nhj';
    }

    public function search_by(ZoneClimatique $zone_climatique): NhjCollection
    {
        return new NhjCollection(\array_map(
            fn(XMLTableElement $record) => $this->to($record),
            $this->createQuery()
                ->and('zone_climatique', $zone_climatique->value)
                ->getMany()
        ));
    }

    protected function to(XMLTableElement $record): Nhj
    {
        return new Nhj(
            mois: Mois::from($record->get('mois')->strval()),
            nhj: $record->get('nhj')->floatval(),
        );
    }
}
