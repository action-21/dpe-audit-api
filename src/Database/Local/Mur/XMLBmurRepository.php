<?php

namespace App\Database\Local\Mur;

use App\Database\Local\{XMLTableElement, XMLTableRepositoryTrait};
use App\Domain\Mur\Data\{Bmur, BmurRepository};
use App\Domain\Mur\Enum\Mitoyennete;

final class XMLBmurRepository implements BmurRepository
{
    use XMLTableRepositoryTrait;

    public static function table(): string
    {
        return 'paroi.b';
    }

    public function find_by(Mitoyennete $mitoyennete): ?Bmur
    {
        $record = $this->createQuery()->and('mitoyennete', $mitoyennete->id())->getOne();
        return $record ? $this->to($record) : null;
    }

    public function to(XMLTableElement $record): Bmur
    {
        return new Bmur(b: $record->get('b')->floatval());
    }
}
