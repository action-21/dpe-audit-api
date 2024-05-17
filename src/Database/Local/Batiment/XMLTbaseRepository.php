<?php

namespace App\Database\Local\Batiment;

use App\Database\Local\{XMLTableElement, XMLTableRepositoryTrait};
use App\Domain\Batiment\Enum\ZoneClimatique;
use App\Domain\Batiment\Table\{Tbase, TbaseRepository};

final class XMLTbaseRepository implements TbaseRepository
{
    use XMLTableRepositoryTrait;

    public static function table(): string
    {
        return 'batiment.tbase.xml';
    }

    public function find(int $id): ?Tbase
    {
        return ($record = $this->createQuery()->and(\sprintf('@id = "%s"', $id))->getOne()) ? $this->to($record) : null;
    }

    public function find_by(ZoneClimatique $zone_climatique, int $altitude): ?Tbase
    {
        $record = $this->createQuery()
            ->and(\sprintf('zone_climatique = "%s"', $zone_climatique->code()))
            ->andCompareTo('altitude', $altitude)
            ->getOne();
        return $record ? $this->to($record) : null;
    }

    protected function to(XMLTableElement $record): Tbase
    {
        return new Tbase(
            id: $record->id(),
            tbase: (float) $record->tbase,
        );
    }
}
