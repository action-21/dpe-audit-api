<?php

namespace App\Database\Local\PlancherHaut;

use App\Database\Local\{XMLTableElement, XMLTableRepositoryTrait};
use App\Domain\PlancherHaut\Data\{Bph, BphRepository};
use App\Domain\PlancherHaut\Enum\Mitoyennete;

final class XMLBphRepository implements BphRepository
{
    use XMLTableRepositoryTrait;

    public static function table(): string
    {
        return 'paroi.b';
    }

    public function find_by(Mitoyennete $mitoyennete): ?Bph
    {
        $record = $this->createQuery()->and('mitoyennete', $mitoyennete->id())->getOne();
        return $record ? $this->to($record) : null;
    }

    public function to(XMLTableElement $record): Bph
    {
        return new Bph((float) $record->b);
    }
}
