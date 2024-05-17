<?php

namespace App\Database\Local\MasqueProche;

use App\Database\Local\{XMLTableElement, XMLTableRepositoryTrait};
use App\Domain\Common\Enum\Orientation;
use App\Domain\MasqueProche\Enum\TypeMasqueProche;
use App\Domain\MasqueProche\Table\{Fe1, Fe1Collection, Fe1Repository};

final class XMLFe1Repository implements Fe1Repository
{
    use XMLTableRepositoryTrait;

    public static function table(): string
    {
        return 'masque_proche.fe1.xml';
    }

    public function find(int $id): ?Fe1
    {
        return ($record = $this->createQuery()->and(\sprintf('@id = "%s"', $id))->getOne()) ? $this->to($record) : null;
    }

    public function find_by(TypeMasqueProche $type_masque_proche, ?Orientation $orientation, ?float $avancee): ?Fe1
    {
        $record = $this->createQuery()
            ->and(\sprintf('type_masque_proche_id = "%s"', $type_masque_proche->id()))
            ->and(\sprintf('orientation = "" or orientation = "%s"', $orientation?->code()))
            ->andCompareTo('avancee', $avancee)
            ->getOne();
        return $record ? $this->to($record) : null;
    }

    public function search_by(
        ?TypeMasqueProche $type_masque_proche = null,
        ?Orientation $orientation = null,
        ?float $avancee = null,
        ?int $tv_coef_masque_proche_id = null
    ): Fe1Collection {
        $this->createQuery();

        if ($type_masque_proche) {
            $this->and(\sprintf('type_masque_proche_id = "%s"', $type_masque_proche->id()));
        }
        if ($orientation) {
            $this->and(\sprintf('orientation = "" or orientation = "%s"', $orientation->code()));
        }
        if ($avancee) {
            $this->andCompareTo('avancee', $avancee);
        }
        if ($tv_coef_masque_proche_id) {
            $this->and(\sprintf('tv_coef_masque_proche_id = "%s"', $tv_coef_masque_proche_id));
        }
        return new Fe1Collection(\array_map(
            fn (XMLTableElement $record): Fe1 => $this->to($record),
            $this->getMany(),
        ));
    }

    public function to(XMLTableElement $record): Fe1
    {
        return new Fe1(
            id: $record->id(),
            type_masque_proche: TypeMasqueProche::from((int) $record->type_masque_proche),
            orientation: ($value = (string) $record->orientation) ? Orientation::from_code($value) : null,
            avancee_defaut: (string) $record->avancee_defaut ? (float) $record->avancee_defaut : null,
            fe1: (float) $record->fe1,
        );
    }
}
