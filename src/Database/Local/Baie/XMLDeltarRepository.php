<?php

namespace App\Database\Local\Baie;

use App\Database\Local\{XMLTableElement, XMLTableRepositoryTrait};
use App\Domain\Baie\Enum\TypeFermeture;
use App\Domain\Baie\Table\{Deltar, DeltarRepository};

final class XMLDeltarRepository implements DeltarRepository
{
    use XMLTableRepositoryTrait;

    public static function table(): string
    {
        return 'baie.deltar.xml';
    }

    public function find(int $id): ?Deltar
    {
        return ($record = $this->createQuery()->and(\sprintf('@id = "%s"', $id))->getOne()) ? $this->to($record) : null;
    }

    public function find_by(TypeFermeture $type_fermeture): ?Deltar
    {
        $record = $this->createQuery()->and(\sprintf('type_fermeture_id = "%s"', $type_fermeture->id()))->getOne();
        return $record ? $this->to($record) : null;
    }

    protected function to(XMLTableElement $record): Deltar
    {
        return new Deltar(
            id: $record->id(),
            deltar: (float) $record->deltar,
        );
    }
}
