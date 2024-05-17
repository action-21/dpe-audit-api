<?php

namespace App\Database\Local\Chauffage;

use App\Database\Local\{XMLTableElement, XMLTableRepositoryTrait};
use App\Domain\Batiment\Enum\{TypeBatiment, ZoneClimatique};
use App\Domain\Chauffage\Table\{Fch, FchRepository};

final class XMLFchRepository implements FchRepository
{
    use XMLTableRepositoryTrait;

    public static function table(): string
    {
        return 'chauffage.fch.xml';
    }

    public function find(int $id): ?Fch
    {
        return ($record = $this->createQuery()->and(\sprintf('@id = "%s"', $id))->getOne()) ? $this->to($record) : null;
    }

    public function find_by(ZoneClimatique $zone_climatique, TypeBatiment $type_batiment): ?Fch
    {
        $record = $this->createQuery()
            ->and(\sprintf('zone_climatique_id = "%s"', $zone_climatique->id()))
            ->and(\sprintf('type_batiment_id = "%s"', $type_batiment->id()))
            ->getOne();
        return $record ? $this->to($record) : null;
    }

    public function to(XMLTableElement $record): Fch
    {
        return new Fch(
            id: $record->id(),
            fch: (float) $record->fch,
        );
    }
}
