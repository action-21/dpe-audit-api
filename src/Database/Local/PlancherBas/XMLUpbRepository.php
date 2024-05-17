<?php

namespace App\Database\Local\PlancherBas;

use App\Database\Local\{XMLTableElement, XMLTableRepositoryTrait};
use App\Domain\Batiment\Enum\ZoneClimatique;
use App\Domain\PlancherBas\Table\{Upb, UpbRepository};

final class XMLUpbRepository implements UpbRepository
{
    use XMLTableRepositoryTrait;

    public static function table(): string
    {
        return 'plancher_bas.upb.xml';
    }

    public function find(int $id): ?Upb
    {
        return ($record = $this->createQuery()->and(\sprintf('@id = "%s"', $id))->getOne()) ? $this->to($record) : null;
    }

    public function find_by(ZoneClimatique $zone_climatique, int $annee_construction_isolation, bool $effet_joule): ?Upb
    {
        $record = $this->createQuery()
            ->and(\sprintf('zone_climatique = "%s"', $zone_climatique->code()))
            ->and(\sprintf('effet_joule = "%s"', (int) $effet_joule))
            ->andCompareTo('annee_construction_isolation', $annee_construction_isolation)
            ->getOne();

        return $record ? $this->to($record) : null;
    }

    protected function to(XMLTableElement $record): Upb
    {
        return new Upb(
            id: $record->id(),
            upb: (float) $record->upb,
        );
    }
}
