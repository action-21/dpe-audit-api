<?php

namespace App\Database\Local\Mur;

use App\Database\Local\{XMLTableElement, XMLTableRepositoryTrait};
use App\Domain\Mur\Data\{Umur0, Umur0Collection, Umur0Repository};
use App\Domain\Mur\Enum\TypeMur;

final class XMLUmur0Repository implements Umur0Repository
{
    use XMLTableRepositoryTrait;

    public static function table(): string
    {
        return 'mur.umur0';
    }

    public function search_by(TypeMur $type_mur): Umur0Collection
    {
        return new Umur0Collection(\array_map(
            fn(XMLTableElement $record): Umur0 => $this->to($record),
            $this->createQuery()->and('type_mur', $type_mur->id())->getMany(),
        ));
    }

    protected function to(XMLTableElement $record): Umur0
    {
        return new Umur0(
            epaisseur: $record->get('epaisseur')->floatval(),
            u0: $record->get('umur0')->floatval(),
        );
    }
}
