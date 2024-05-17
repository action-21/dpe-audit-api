<?php

namespace App\Database\Local\Ecs;

use App\Database\Local\{XMLTableElement, XMLTableRepositoryTrait};
use App\Domain\Batiment\Enum\ZoneClimatique;
use App\Domain\Ecs\Enum\TypeInstallation;
use App\Domain\Ecs\Table\{Cop, CopRepository};

final class XMLCopRepository implements CopRepository
{
    use XMLTableRepositoryTrait;

    public static function table(): string
    {
        return 'ecs.generateur.cop.xml';
    }

    public function find(int $id): ?Cop
    {
        return ($record = $this->createQuery()->and(\sprintf('@id = "%s"', $id))->getOne()) ? $this->to($record) : null;
    }

    public function find_by(ZoneClimatique $zone_climatique, TypeInstallation $type_installation, int $annee_installation): ?Cop
    {
        $record = $this->createQuery()
            ->and(\sprintf('zone_climatique_id = "%s"', $zone_climatique->id()))
            ->and(\sprintf('type_installation_id = "%s"', $type_installation->id()))
            ->andCompareTo('annee_installation', $annee_installation)
            ->getOne();
        return $record ? $this->to($record) : null;
    }

    protected function to(XMLTableElement $record): Cop
    {
        return new Cop(
            id: $record->id(),
            cop: (float) $record->cop,
        );
    }
}
