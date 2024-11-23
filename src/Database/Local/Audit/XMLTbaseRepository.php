<?php

namespace App\Database\Local\Audit;

use App\Domain\Audit\Data\{Tbase, TbaseRepository};
use App\Database\Local\{XMLTableElement, XMLTableRepositoryTrait};
use App\Domain\Common\Enum\{ZoneClimatique};

final class XMLTbaseRepository implements TbaseRepository
{
    use XMLTableRepositoryTrait;

    public static function table(): string
    {
        return 'audit.tbase';
    }

    public function find_by(ZoneClimatique $zone_climatique, int $altitude): ?Tbase
    {
        $record = $this->createQuery()
            ->and('zone_climatique', $zone_climatique->code())
            ->andCompareTo('altitude', $altitude)
            ->getOne();
        return $record ? $this->to($record) : null;
    }

    protected function to(XMLTableElement $record): Tbase
    {
        return new Tbase(tbase: $record->get('tbase')->floatval(),);
    }
}
