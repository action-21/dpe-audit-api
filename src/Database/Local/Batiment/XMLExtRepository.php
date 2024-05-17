<?php

namespace App\Database\Local\Batiment;

use App\Database\Local\{XMLTableElement, XMLTableRepositoryTrait};
use App\Domain\Batiment\Enum\ZoneClimatique;
use App\Domain\Batiment\Table\SollicitationExterieure;
use App\Domain\Batiment\Table\SollicitationExterieureCollection;
use App\Domain\Batiment\Table\SollicitationExterieureRepository;
use App\Domain\Common\Enum\Mois;

final class XMLExtRepository implements SollicitationExterieureRepository
{
    use XMLTableRepositoryTrait;

    public static function table(): string
    {
        return 'batiment.ext.xml';
    }

    public function search(int $id): SollicitationExterieureCollection
    {
        return new SollicitationExterieureCollection(\array_map(
            fn (XMLTableElement $record): SollicitationExterieure => $this->to($record),
            $this->createQuery()->and(\sprintf('@id = "%s"', $id))->getMany(),
        ));
    }

    public function search_by(ZoneClimatique $zone_climatique, int $altitude, bool $parois_anciennes_lourdes): SollicitationExterieureCollection
    {
        return new SollicitationExterieureCollection(\array_map(
            fn (XMLTableElement $record): SollicitationExterieure => $this->to($record),
            $this->createQuery()
                ->and(\sprintf('zone_climatique = "%s"', $zone_climatique->lib()))
                ->and(\sprintf('parois_anciennes_lourdes = "%s"', (int) $parois_anciennes_lourdes))
                ->andCompareTo('altitude', $altitude)
                ->getMany()
        ));
    }

    protected function to(XMLTableElement $record): SollicitationExterieure
    {
        return new SollicitationExterieure(
            id: $record->id(),
            mois: Mois::from_iso((string) $record->mois),
            epv: (float) $record->epv,
            e: (string) $record->e ? (float) $record->e : null,
            efr26: (string) $record->efr26 ? (float) $record->efr26 : null,
            efr28: (string) $record->efr28 ? (float) $record->efr28 : null,
            text: (string) $record->text ? (float) $record->text : null,
            textmoy_clim26: (string) $record->textmoy_clim26 ? (float) $record->textmoy_clim26 : null,
            textmoy_clim28: (string) $record->textmoy_clim28 ? (float) $record->textmoy_clim28 : null,
            nref19: (string) $record->nref19 ? (float) $record->nref19 : null,
            nref21: (string) $record->nref21 ? (float) $record->nref21 : null,
            nref26: (string) $record->nref26 ? (float) $record->nref26 : null,
            nref28: (string) $record->nref28 ? (float) $record->nref28 : null,
            dh14: (string) $record->dh14 ? (float) $record->dh14 : null,
            dh19: (string) $record->dh19 ? (float) $record->dh19 : null,
            dh21: (string) $record->dh21 ? (float) $record->dh21 : null,
            dh26: (string) $record->dh26 ? (float) $record->dh26 : null,
            dh28: (string) $record->dh28 ? (float) $record->dh28 : null,
            tefs: (string) $record->tefs ? (float) $record->tefs : null
        );
    }
}
