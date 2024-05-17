<?php

namespace App\Database\Local\Baie;

use App\Database\Local\{XMLTableElement, XMLTableRepositoryTrait};
use App\Domain\Baie\Table\Deltar;
use App\Domain\Baie\Table\{Ujn, UjnCollection, UjnRepository};

final class XMLUjnRepository implements UjnRepository
{
    use XMLTableRepositoryTrait;

    public static function table(): string
    {
        return 'baie.ujn.xml';
    }

    public function search(int $id): UjnCollection
    {
        return new UjnCollection(\array_map(
            fn (XMLTableElement $record): Ujn => $this->to($record),
            $this->createQuery()->and(\sprintf('@id = "%s"', $id))->getMany(),
        ));
    }

    public function search_by(Deltar $deltar): UjnCollection
    {
        return new UjnCollection(\array_map(
            fn (XMLTableElement $record): Ujn => $this->to($record),
            $this->createQuery()->and(\sprintf('deltar = "%s"', $deltar->valeur()))->getMany(),
        ));
    }

    protected function to(XMLTableElement $record): Ujn
    {
        return new Ujn(
            id: $record->id(),
            uw: (float) $record->uw,
            ujn: (float) $record->ujn,
        );
    }
}
