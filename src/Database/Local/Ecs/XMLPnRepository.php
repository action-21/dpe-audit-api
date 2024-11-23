<?php

namespace App\Database\Local\Ecs;

use App\Domain\Ecs\Data\{Pn, PnRepository};
use App\Database\Local\{XMLTableElement, XMLTableRepositoryTrait};
use App\Domain\Ecs\Enum\{TypeChaudiere};

final class XMLPnRepository implements PnRepository
{
    use XMLTableRepositoryTrait;

    public static function table(): string
    {
        return 'ecs.pn';
    }

    public function find_by(TypeChaudiere $type_chaudiere, int $annee_installation, float $pdim): ?Pn
    {
        $record = $this->createQuery()
            ->and('type_chaudiere', $type_chaudiere->id())
            ->andCompareTo('annee_installation_generateur', $annee_installation)
            ->andCompareTo('pdim', $pdim)
            ->getOne();
        return $record ? $this->to($record) : null;
    }

    protected function to(XMLTableElement $record): Pn
    {
        return new Pn(pn: $record->get('pn')->strval(),);
    }
}
