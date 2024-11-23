<?php

namespace App\Database\Local\PlancherBas;

use App\Database\Local\{XMLTableElement, XMLTableRepositoryTrait};
use App\Domain\Common\Enum\ZoneClimatique;
use App\Domain\PlancherBas\Data\{Upb, UpbRepository};

final class XMLUpbRepository implements UpbRepository
{
    use XMLTableRepositoryTrait;

    public static function table(): string
    {
        return 'plancher_bas.upb';
    }

    public function find_by(ZoneClimatique $zone_climatique, int $annee_construction_isolation, bool $effet_joule): ?Upb
    {
        $record = $this->createQuery()
            ->and('zone_climatique', $zone_climatique->code())
            ->and('effet_joule', $effet_joule)
            ->andCompareTo('annee_construction_isolation', $annee_construction_isolation)
            ->getOne();

        return $record ? $this->to($record) : null;
    }

    protected function to(XMLTableElement $record): Upb
    {
        return new Upb(upb: $record->get('upb')->floatval());
    }
}
