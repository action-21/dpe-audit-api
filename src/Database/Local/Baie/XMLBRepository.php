<?php

namespace App\Database\Local\Baie;

use App\Database\Local\{XMLTableElement, XMLTableRepositoryTrait};
use App\Domain\Baie\Enum\Mitoyennete;
use App\Domain\Baie\Table\{B, BRepository};

final class XMLBRepository implements BRepository
{
    use XMLTableRepositoryTrait;

    public static function table(): string
    {
        return 'baie.b.xml';
    }

    public function find(int $id): ?B
    {
        return ($record = $this->createQuery()->and(\sprintf('@id = "%s"', $id))->getOne()) ? $this->to($record) : null;
    }

    public function find_by(Mitoyennete $mitoyennete): ?B
    {
        $record = $this->createQuery()->and(\sprintf('mitoyennete_id = "%s"', $mitoyennete->id()))->getOne();
        return $record ? $this->to($record) : null;
    }

    public function to(XMLTableElement $record): B
    {
        return new B(id: $record->id(), b: (float) $record->b);
    }
}
