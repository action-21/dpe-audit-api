<?php

namespace App\Database\Local\Refroidissement;

use App\Database\Local\{XMLTableElement, XMLTableRepositoryTrait};
use App\Domain\Common\Enum\ZoneClimatique;
use App\Domain\Refroidissement\Data\{Eer, EerRepository};

final class XMLEerRepository implements EerRepository
{
    use XMLTableRepositoryTrait;

    public static function table(): string
    {
        return 'refroidissement.eer';
    }

    public function find_by(ZoneClimatique $zone_climatique, int $annee_installation_generateur): ?Eer
    {
        $record = $this->createQuery()
            ->and('zone_climatique', $zone_climatique->code())
            ->andCompareTo('annee_installation_generateur', $annee_installation_generateur)
            ->getOne();
        return $record ? $this->to($record) : null;
    }

    public function to(XMLTableElement $element): Eer
    {
        return new Eer(eer: $element->get('eer')->floatval());
    }
}
