<?php

namespace App\Database\Local\Audit;

use App\Domain\Audit\Data\{SollicitationExterieure, SollicitationExterieureCollection, SollicitationExterieureRepository};
use App\Database\Local\{XMLTableElement, XMLTableRepositoryTrait};
use App\Domain\Common\Enum\{Mois, ZoneClimatique};

final class XMLSollicitationExterieureRepository implements SollicitationExterieureRepository
{
    use XMLTableRepositoryTrait;

    public static function table(): string
    {
        return 'audit.ext';
    }

    public function search_by(
        ZoneClimatique $zone_climatique,
        int $altitude,
        bool $parois_anciennes_lourdes,
    ): SollicitationExterieureCollection {
        return new SollicitationExterieureCollection(\array_map(
            fn(XMLTableElement $record) => $this->to($record),
            $this->createQuery()
                ->and('zone_climatique', $zone_climatique->value)
                ->and('parois_anciennes_lourdes', $parois_anciennes_lourdes)
                ->andCompareTo('altitude', $altitude)
                ->getMany()
        ));
    }

    protected function to(XMLTableElement $record): SollicitationExterieure
    {
        return new SollicitationExterieure(
            mois: Mois::from($record->get('mois')->strval()),
            epv: $record->get('epv')->floatval(),
            e: $record->get('e')->floatval(),
            efr26: $record->get('efr26')->floatval(),
            efr28: $record->get('efr28')->floatval(),
            text: $record->get('text')->floatval(),
            textmoy_clim26: $record->get('textmoy_clim26')->floatval(),
            textmoy_clim28: $record->get('textmoy_clim28')->floatval(),
            nref19: $record->get('nref19')->floatval(),
            nref21: $record->get('nref21')->floatval(),
            nref26: $record->get('nref26')->floatval(),
            nref28: $record->get('nref28')->floatval(),
            dh14: $record->get('dh14')->floatval(),
            dh19: $record->get('dh19')->floatval(),
            dh21: $record->get('dh21')->floatval(),
            dh26: $record->get('dh26')->floatval(),
            dh28: $record->get('dh28')->floatval(),
            tefs: $record->get('tefs')->floatval(),
        );
    }
}
