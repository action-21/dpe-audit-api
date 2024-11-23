<?php

namespace App\Database\Local\Baie;

use App\Database\Local\{XMLTableElement, XMLTableRepositoryTrait};
use App\Domain\Baie\Data\{Deltar, DeltarRepository};
use App\Domain\Baie\Enum\TypeFermeture;

final class XMLDeltarRepository implements DeltarRepository
{
    use XMLTableRepositoryTrait;

    public static function table(): string
    {
        return 'baie.deltar';
    }

    public function find_by(TypeFermeture $type_fermeture): ?Deltar
    {
        $record = $this->createQuery()->and('type_fermeture', $type_fermeture->id())->getOne();
        return $record ? $this->to($record) : null;
    }

    protected function to(XMLTableElement $record): Deltar
    {
        return new Deltar(deltar: $record->get('deltar')->floatval(),);
    }
}
