<?php

namespace App\Database\Local\Lnc;

use App\Database\Local\{XMLTableElement, XMLTableRepositoryTrait};
use App\Domain\Lnc\Data\{Uvue, UvueRepository};
use App\Domain\Lnc\Enum\TypeLnc;

final class XMLUvueRepository implements UvueRepository
{
    use XMLTableRepositoryTrait;

    public static function table(): string
    {
        return 'lnc.uvue';
    }

    public function find_by(TypeLnc $type_lnc): ?Uvue
    {
        $record = $this->createQuery()->and('type_local_non_chauffe', $type_lnc->id())->getOne();
        return $record ? $this->to($record) : null;
    }

    public function to(XMLTableElement $record): Uvue
    {
        return new Uvue(uvue: (float) $record->uvue);
    }
}
