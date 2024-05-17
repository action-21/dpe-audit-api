<?php

namespace App\Database\Local\Ecs;

use App\Database\Local\{XMLTableElement, XMLTableRepositoryTrait};
use App\Domain\Batiment\Enum\{TypeBatiment, ZoneClimatique};
use App\Domain\Ecs\Enum\TypeInstallationSolaire;
use App\Domain\Ecs\Table\{Fecs, FecsRepository};

final class XMLFecsRepository implements FecsRepository
{
    use XMLTableRepositoryTrait;

    public static function table(): string
    {
        return 'ecs.fecs.xml';
    }

    public function find(int $id): ?Fecs
    {
        return ($record = $this->createQuery()->and(\sprintf('@id = "%s"', $id))->getOne()) ? $this->to($record) : null;
    }

    public function find_by(
        ZoneClimatique $zone_climatique,
        TypeBatiment $type_batiment,
        TypeInstallationSolaire $type_installation_solaire
    ): ?Fecs {
        $record = $this->createQuery()
            ->and(\sprintf('zone_climatique_id = "%s"', $zone_climatique->id()))
            ->and(\sprintf('type_batiment_id = "%s"', $type_batiment->id()))
            ->and(\sprintf('type_installation_solaire_id = "%s"', $type_installation_solaire->id()))
            ->getOne();
        return $record ? $this->to($record) : null;
    }

    protected function to(XMLTableElement $record): Fecs
    {
        return new Fecs(
            id: $record->id(),
            fecs: (float) $record->fecs,
        );
    }
}
