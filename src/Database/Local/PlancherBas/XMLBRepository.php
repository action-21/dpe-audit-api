<?php

namespace App\Database\Local\PlancherBas;

use App\Database\Local\{XMLTableElement, XMLTableRepositoryTrait};
use App\Domain\PlancherBas\Enum\Mitoyennete;
use App\Domain\PlancherBas\Table\{B, BRepository};

final class XMLBRepository implements BRepository
{
    use XMLTableRepositoryTrait;

    public static function table(): string
    {
        return 'plancher_bas.b.xml';
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
