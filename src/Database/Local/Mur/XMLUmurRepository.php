<?php

namespace App\Database\Local\Mur;

use App\Database\Local\{XMLTableElement, XMLTableRepositoryTrait};
use App\Domain\Common\Enum\ZoneClimatique;
use App\Domain\Mur\Data\{Umur, UmurRepository};

final class XMLUmurRepository implements UmurRepository
{
    use XMLTableRepositoryTrait;

    public static function table(): string
    {
        return 'mur.umur';
    }

    public function find_by(ZoneClimatique $zone_climatique, int $annee_construction_isolation, bool $effet_joule): ?Umur
    {
        $record = $this->createQuery()
            ->and('zone_climatique', $zone_climatique->code())
            ->and('effet_joule', $effet_joule)
            ->andCompareTo('annee_construction_isolation', $annee_construction_isolation)
            ->getOne();
        return $record ? $this->to($record) : null;
    }

    protected function to(XMLTableElement $record): Umur
    {
        return new Umur(u: $record->get('umur')->floatval());
    }
}
