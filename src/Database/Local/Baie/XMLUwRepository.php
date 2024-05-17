<?php

namespace App\Database\Local\Baie;

use App\Database\Local\{XMLTableElement, XMLTableRepositoryTrait};
use App\Domain\Baie\Enum\{TypeBaie, NatureMenuiserie};
use App\Domain\Baie\Table\{Uw, UwCollection, UwRepository};

final class XMLUwRepository implements UwRepository
{
    use XMLTableRepositoryTrait;

    public static function table(): string
    {
        return 'baie.uw.xml';
    }

    public function search(int $id): UwCollection
    {
        return new UwCollection(\array_map(
            fn (XMLTableElement $record): Uw => $this->to($record),
            $this->createQuery()->and(\sprintf('@id = "%s"', $id))->getMany(),
        ));
    }

    public function search_by(TypeBaie $type_baie, NatureMenuiserie $nature_menuiserie): UwCollection
    {
        return new UwCollection(\array_map(
            fn (XMLTableElement $record): Uw => $this->to($record),
            $this->createQuery()
                ->and(\sprintf('type_baie = "%s"', $type_baie->id()))
                ->and(\sprintf('nature_menuiserie = "%s"', $nature_menuiserie->id()))
                ->getMany(),
        ));
    }

    protected function to(XMLTableElement $record): Uw
    {
        return new Uw(
            id: $record->id(),
            ug: (string) $record->ug ? (float) $record->ug : null,
            uw: (float) $record->uw,
        );
    }
}
