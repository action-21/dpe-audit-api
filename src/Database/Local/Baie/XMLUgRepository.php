<?php

namespace App\Database\Local\Baie;

use App\Database\Local\{XMLTableElement, XMLTableRepositoryTrait};
use App\Domain\Baie\Enum\{NatureGazLame, TypeVitrage};
use App\Domain\Baie\Table\{Ug, UgRepository, UgCollection};

final class XMLUgRepository implements UgRepository
{
    use XMLTableRepositoryTrait;

    public static function table(): string
    {
        return 'baie.ug.xml';
    }

    public function search(int $id): UgCollection
    {
        return new UgCollection(\array_map(
            fn (XMLTableElement $record): Ug => $this->to($record),
            $this->createQuery()->and(\sprintf('@id = "%s"', $id))->getMany(),
        ));
    }

    public function search_by(
        TypeVitrage $type_vitrage,
        ?NatureGazLame $nature_gaz_lame,
        ?int $inclinaison_vitrage,
    ): UgCollection {
        return new UgCollection(\array_map(
            fn (XMLTableElement $record): Ug => $this->to($record),
            $this->createQuery()
                ->and(\sprintf('type_vitrage_id = "%s"', $type_vitrage->id()))
                ->and(\sprintf('nature_gaz_lame_id = "%s" or nature_gaz_lame_id = ""', $nature_gaz_lame?->id()))
                ->andCompareTo('inclinaison', $inclinaison_vitrage)
                ->getMany()
        ));
    }

    protected function to(XMLTableElement $record): Ug
    {
        return new Ug(
            id: $record->id(),
            epaisseur_lame: (string) $record->epaisseur_lame ? (float) $record->epaisseur_lame : null,
            ug: (float) $record->ug,
        );
    }
}
