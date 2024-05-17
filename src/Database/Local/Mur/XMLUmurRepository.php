<?php

namespace App\Database\Local\Mur;

use App\Database\Local\{XMLTableElement, XMLTableRepositoryTrait};
use App\Domain\Batiment\Enum\ZoneClimatique;
use App\Domain\Mur\Table\{Umur, UmurRepository};

final class XMLUmurRepository implements UmurRepository
{
    use XMLTableRepositoryTrait;

    public static function table(): string
    {
        return 'mur.umur.xml';
    }

    public function find(int $id): ?Umur
    {
        return ($record = $this->createQuery()->and(\sprintf('@id = "%s"', $id))->getOne()) ? $this->to($record) : null;
    }

    public function find_by(ZoneClimatique $zone_climatique, int $annee_construction_isolation, bool $effet_joule): ?Umur
    {
        $record = $this->createQuery()
            ->and(\sprintf('zone_climatique = "%s"', $zone_climatique->code()))
            ->and(\sprintf('effet_joule = "%s"', (int) $effet_joule))
            ->andCompareTo('annee_construction_isolation', $annee_construction_isolation)
            ->getOne();
        return $record ? $this->to($record) : null;
    }

    protected function to(XMLTableElement $record): Umur
    {
        return new Umur(
            id: $record->id(),
            umur: (float) $record->umur,
        );
    }
}
