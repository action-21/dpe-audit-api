<?php

namespace App\Database\Local\Porte;

use App\Database\Local\{XMLTableElement, XMLTableRepositoryTrait};
use App\Domain\Porte\Data\{Bporte, BporteRepository};
use App\Domain\Porte\Enum\Mitoyennete;

final class XMLBporteRepository implements BporteRepository
{
    use XMLTableRepositoryTrait;

    public static function table(): string
    {
        return 'paroi.b';
    }

    public function find_by(Mitoyennete $mitoyennete): ?Bporte
    {
        $record = $this->createQuery()->and('mitoyennete', $mitoyennete->id())->getOne();
        return $record ? $this->to($record) : null;
    }

    public function to(XMLTableElement $record): Bporte
    {
        return new Bporte(b: $record->get('b')->floatval());
    }
}
