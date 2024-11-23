<?php

namespace App\Database\Local\PlancherBas;

use App\Database\Local\{XMLTableElement, XMLTableRepositoryTrait};
use App\Domain\PlancherBas\Enum\Mitoyennete;
use App\Domain\PlancherBas\Data\{Ue, UeCollection, UeRepository};

final class XMLUeRepository implements UeRepository
{
    use XMLTableRepositoryTrait;

    public static function table(): string
    {
        return 'plancher_bas.ue';
    }

    public function search_by(Mitoyennete $mitoyennete, int $annee_construction): UeCollection
    {
        return new UeCollection(\array_map(
            fn(XMLTableElement $record): Ue => $this->to($record),
            $this->createQuery()
                ->and('mitoyennete', $mitoyennete->id())
                ->andCompareTo('annee_construction', $annee_construction)
                ->getMany()
        ));
    }

    protected function to(XMLTableElement $record): Ue
    {
        return new Ue(
            ue: $record->get('ue')->floatval(),
            upb: $record->get('upb')->floatval(),
            _2sp: $record->get('_2s_p')->floatval(),
        );
    }
}
