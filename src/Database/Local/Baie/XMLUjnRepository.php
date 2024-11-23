<?php

namespace App\Database\Local\Baie;

use App\Database\Local\{XMLTableElement, XMLTableRepositoryTrait};
use App\Domain\Baie\Data\{Ujn, UjnCollection, UjnRepository};

final class XMLUjnRepository implements UjnRepository
{
    use XMLTableRepositoryTrait;

    public static function table(): string
    {
        return 'baie.ujn';
    }

    public function search_by(float $deltar): UjnCollection
    {
        return new UjnCollection(\array_map(
            fn(XMLTableElement $record): Ujn => $this->to($record),
            $this->createQuery()->and('deltar', $deltar)->getMany(),
        ));
    }

    protected function to(XMLTableElement $record): Ujn
    {
        return new Ujn(
            uw: $record->get('uw')->floatval(),
            ujn: $record->get('ujn')->floatval(),
        );
    }
}
