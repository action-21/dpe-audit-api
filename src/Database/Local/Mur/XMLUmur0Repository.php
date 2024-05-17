<?php

namespace App\Database\Local\Mur;

use App\Database\Local\{XMLTableElement, XMLTableRepositoryTrait};
use App\Domain\Mur\Enum\TypeMur;
use App\Domain\Mur\Table\{Umur0, Umur0Collection, Umur0Repository};

final class XMLUmur0Repository implements Umur0Repository
{
    use XMLTableRepositoryTrait;

    public static function table(): string
    {
        return 'mur.umur0.xml';
    }

    public function find(int $id): ?Umur0
    {
        return ($record = $this->createQuery()->and(\sprintf('@id = "%s"', $id))->getOne()) ? $this->to($record) : null;
    }

    public function find_by(TypeMur $type_mur, ?float $epaisseur): ?Umur0
    {
        $record = $this->createQuery()
            ->and(\sprintf('type_mur_id = "%s"', $type_mur->id()))
            ->and(\sprintf('epaisseur = "%s" or epaisseur = ""', $epaisseur))
            ->getOne();
        return $record ? $this->to($record) : null;
    }

    public function search(int $id): Umur0Collection
    {
        return new Umur0Collection(\array_map(
            fn (XMLTableElement $record): Umur0 => $this->to($record),
            $this->createQuery()->and(\sprintf('@id = "%s"', $id))->getMany(),
        ));
    }

    public function search_by(TypeMur $type_mur): Umur0Collection
    {
        return new Umur0Collection(\array_map(
            fn (XMLTableElement $record): Umur0 => $this->to($record),
            $this->createQuery()->and(\sprintf('type_mur_id = "%s"', $type_mur->id()))->getMany(),
        ));
    }

    protected function to(XMLTableElement $record): Umur0
    {
        return new Umur0(
            id: $record->id(),
            epaisseur: (string) $record->epaisseur ? (float) $record->epaisseur : null,
            umur0: (float) $record->umur0,
        );
    }
}
