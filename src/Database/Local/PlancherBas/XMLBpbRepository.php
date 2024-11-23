<?php

namespace App\Database\Local\PlancherBas;

use App\Database\Local\{XMLTableElement, XMLTableRepositoryTrait};
use App\Domain\PlancherBas\Data\{Bpb, BpbRepository};
use App\Domain\PlancherBas\Enum\Mitoyennete;

final class XMLBpbRepository implements BpbRepository
{
    use XMLTableRepositoryTrait;

    public static function table(): string
    {
        return 'paroi.b';
    }

    public function find_by(Mitoyennete $mitoyennete): ?Bpb
    {
        $record = $this->createQuery()->and('mitoyennete', $mitoyennete->id())->getOne();
        return $record ? $this->to($record) : null;
    }

    public function to(XMLTableElement $record): Bpb
    {
        return new Bpb(b: $record->get('b')->floatval());
    }
}
