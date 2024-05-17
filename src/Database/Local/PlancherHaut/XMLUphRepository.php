<?php

namespace App\Database\Local\PlancherHaut;

use App\Database\Local\{XMLTableElement, XMLTableRepositoryTrait};
use App\Domain\Batiment\Enum\ZoneClimatique;
use App\Domain\PlancherHaut\Enum\ConfigurationPlancherHaut;
use App\Domain\PlancherHaut\Table\{Uph, UphRepository};

final class XMLUphRepository implements UphRepository
{
    use XMLTableRepositoryTrait;

    public static function table(): string
    {
        return 'plancher_haut.uph.xml';
    }

    public function find(int $id): ?Uph
    {
        return ($record = $this->createQuery()->and(\sprintf('@id = "%s"', $id))->getOne()) ? $this->to($record) : null;
    }

    public function find_by(ZoneClimatique $zone_climatique, ConfigurationPlancherHaut $configuration_plancher_haut, int $annee_construction_isolation, bool $effet_joule): ?Uph
    {
        $record = $this->createQuery()
            ->and(\sprintf('zone_climatique = "%s"', $zone_climatique->code()))
            ->and(\sprintf('combles = "%s"', $configuration_plancher_haut->combles() ? 1 : 0))
            ->and(\sprintf('effet_joule = "%s"', (int) $effet_joule))
            ->andCompareTo('annee_construction_isolation', $annee_construction_isolation)
            ->getOne();
        return $record ? $this->to($record) : null;
    }

    protected function to(XMLTableElement $record): Uph
    {
        return new Uph(
            id: $record->id(),
            effet_joule: (bool) $record->effet_joule,
            combles: (bool) $record->combles,
            terrasse: (bool) $record->terrasse,
            uph: (float) $record->uph,
            tv_uph_id: (int) $record->tv_uph_id,
        );
    }
}
