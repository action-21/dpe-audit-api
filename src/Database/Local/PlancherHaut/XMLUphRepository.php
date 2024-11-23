<?php

namespace App\Database\Local\PlancherHaut;

use App\Database\Local\{XMLTableElement, XMLTableRepositoryTrait};
use App\Domain\Common\Enum\ZoneClimatique;
use App\Domain\PlancherHaut\Data\{Uph, UphRepository};
use App\Domain\PlancherHaut\Enum\Categorie;

final class XMLUphRepository implements UphRepository
{
    use XMLTableRepositoryTrait;

    public static function table(): string
    {
        return 'plancher_haut.uph';
    }

    public function find_by(
        ZoneClimatique $zone_climatique,
        Categorie $categorie,
        int $annee_construction_isolation,
        bool $effet_joule
    ): ?Uph {
        $record = $this->createQuery()
            ->and('zone_climatique', $zone_climatique->code())
            ->and('categorie', $categorie->id())
            ->and('effet_joule', $effet_joule)
            ->andCompareTo('annee_construction_isolation', $annee_construction_isolation)
            ->getOne();
        return $record ? $this->to($record) : null;
    }

    protected function to(XMLTableElement $record): Uph
    {
        return new Uph(u: $record->get('uph')->floatval());
    }
}
