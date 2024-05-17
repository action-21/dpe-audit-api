<?php

namespace App\Database\Local\Climatisation;

use App\Database\Local\{XMLTableElement, XMLTableRepositoryTrait};
use App\Domain\Batiment\Enum\ZoneClimatique;
use App\Domain\Climatisation\Table\{Seer, SeerRepository};

final class XMLSeerRepository implements SeerRepository
{
    use XMLTableRepositoryTrait;

    public static function table(): string
    {
        return 'climatisation.seer.xml';
    }

    public function find(int $id): ?Seer
    {
        return ($record = $this->createQuery()->and(\sprintf('@id = "%s"', $id))->getOne()) ? $this->to($record) : null;
    }

    public function find_by(ZoneClimatique $zone_climatique, int $annee_installation): ?Seer
    {
        $record = $this->createQuery()
            ->and(\sprintf('zone_climatique = "%s"', $zone_climatique->code()))
            ->andCompareTo('annee_installation', $annee_installation)
            ->getOne();
        return $record ? $this->to($record) : null;
    }

    public function to(XMLTableElement $record): Seer
    {
        return new Seer(id: $record->id(), eer: (float) $record->eer);
    }
}
