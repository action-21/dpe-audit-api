<?php

namespace App\Database\Local\Chauffage;

use App\Domain\Chauffage\Data\{Fch, FchRepository};
use App\Database\Local\{XMLTableElement, XMLTableRepositoryTrait};
use App\Domain\Common\Enum\{Enum, ZoneClimatique};

final class XMLFchRepository implements FchRepository
{
    use XMLTableRepositoryTrait;

    public static function table(): string
    {
        return 'chauffage.fch';
    }

    public function find_by(Enum $type_batiment, ZoneClimatique $zone_climatique,): ?Fch
    {
        $record = $this->createQuery()
            ->and('type_batiment', $type_batiment->value)
            ->and('zone_climatique', $zone_climatique->value)
            ->getOne();
        return $record ? $this->to($record) : null;
    }

    protected function to(XMLTableElement $record): Fch
    {
        return new Fch(fch: $record->get('fch')->floatval(),);
    }
}
