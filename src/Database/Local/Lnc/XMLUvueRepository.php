<?php

namespace App\Database\Local\Lnc;

use App\Database\Local\{XMLTableElement, XMLTableRepositoryTrait};
use App\Domain\Lnc\Enum\TypeLnc;
use App\Domain\Lnc\Table\{Uvue, UvueRepository};

final class XMLUvueRepository implements UvueRepository
{
    use XMLTableRepositoryTrait;

    public static function table(): string
    {
        return 'lnc.uvue.xml';
    }

    public function find(int $id): ?Uvue
    {
        return ($record = $this->createQuery()->and(\sprintf('@id = "%s"', $id))->getOne()) ? $this->to($record) : null;
    }

    public function find_by(TypeLnc $type_lnc): ?Uvue
    {
        $record = $this->createQuery()->and(\sprintf('type_lnc_id = "%s"', $type_lnc->id()))->getOne();
        return $record ? $this->to($record) : null;
    }

    public function to(XMLTableElement $record): Uvue
    {
        return new Uvue(id: $record->id(), uvue: (float) $record->uvue);
    }
}
