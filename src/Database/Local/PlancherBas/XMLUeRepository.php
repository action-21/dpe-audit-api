<?php

namespace App\Database\Local\PlancherBas;

use App\Database\Local\{XMLTableElement, XMLTableRepositoryTrait};
use App\Domain\PlancherBas\Enum\Mitoyennete;
use App\Domain\PlancherBas\Table\{Ue, UeCollection, UeRepository};

final class XMLUeRepository implements UeRepository
{
    use XMLTableRepositoryTrait;

    public static function table(): string
    {
        return 'plancher_bas.ue.xml';
    }

    public function search(int $id): UeCollection
    {
        return new UeCollection(\array_map(
            fn (XMLTableElement $record): Ue => $this->to($record),
            $this->createQuery()->and(\sprintf('@id = "%s"', $id))->getMany()
        ));
    }

    public function search_by(Mitoyennete $mitoyennete, int $annee_construction): UeCollection
    {
        return new UeCollection(\array_map(
            fn (XMLTableElement $record): Ue => $this->to($record),
            $this->createQuery()
                ->and(\sprintf('mitoyennete_id = "%s"', $mitoyennete->id()))
                ->andCompareTo('annee_construction', $annee_construction)
                ->getMany()
        ));
    }

    protected function to(XMLTableElement $record): Ue
    {
        return new Ue(
            id: $record->id(),
            upb: (float) $record->upb,
            _2sp: (float) $record->_2sp,
            ue: (float) $record->ue,
        );
    }
}
