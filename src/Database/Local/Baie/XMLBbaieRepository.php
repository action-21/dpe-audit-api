<?php

namespace App\Database\Local\Baie;

use App\Database\Local\{XMLTableElement, XMLTableRepositoryTrait};
use App\Domain\Baie\Data\{Bbaie, BbaieRepository};
use App\Domain\Baie\Enum\Mitoyennete;

final class XMLBbaieRepository implements BbaieRepository
{
    use XMLTableRepositoryTrait;

    public static function table(): string
    {
        return 'paroi.b';
    }

    public function find_by(Mitoyennete $mitoyennete): ?Bbaie
    {
        $record = $this->createQuery()->and('mitoyennete', $mitoyennete->id())->getOne();
        return $record ? $this->to($record) : null;
    }

    public function to(XMLTableElement $record): Bbaie
    {
        return new Bbaie($record->get('b')->floatval());
    }
}
