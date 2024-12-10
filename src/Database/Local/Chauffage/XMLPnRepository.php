<?php

namespace App\Database\Local\Chauffage;

use App\Domain\Chauffage\Data\{Pn, PnRepository};
use App\Database\Local\{XMLTableElement, XMLTableRepositoryTrait};
use App\Domain\Chauffage\Enum\PositionChaudiere;

final class XMLPnRepository implements PnRepository
{
    use XMLTableRepositoryTrait;

    public static function table(): string
    {
        return 'chauffage.pn';
    }

    public function find_by(PositionChaudiere $position_chaudiere, int $annee_installation, float $pdim): ?Pn
    {
        $record = $this->createQuery()
            ->and('position_chaudiere', $position_chaudiere->value)
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
