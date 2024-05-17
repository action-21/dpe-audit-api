<?php

namespace App\Database\Local\Lnc;

use App\Database\Local\{XMLTableElement, XMLTableRepositoryTrait};
use App\Domain\Lnc\Enum\{NatureMenuiserie, TypeVitrage};
use App\Domain\Lnc\Table\{T, TCollection, TRepository};

final class XMLTRepository implements TRepository
{
    use XMLTableRepositoryTrait;

    public static function table(): string
    {
        return 'lnc.baie.t.xml';
    }

    public function find(int $id): ?T
    {
        return ($record = $this->createQuery()->and(\sprintf('@id = "%s"', $id))->getOne()) ? $this->to($record) : null;
    }

    public function find_by(NatureMenuiserie $nature_menuiserie, ?TypeVitrage $type_vitrage): ?T
    {
        $record = $this->createQuery()
            ->and(\sprintf('nature_menuiserie_id = "%s"', $nature_menuiserie->id()))
            ->and(\sprintf('type_vitrage_id = "%s" or type_vitrage_id = ""', $type_vitrage?->id()))
            ->getOne();
        return $record ? $this->to($record) : null;
    }

    public function search_by(
        ?NatureMenuiserie $nature_menuiserie = null,
        ?TypeVitrage $type_vitrage = null,
        ?int $tv_coef_transparence_ets_id = null
    ): TCollection {
        $this->createQuery();

        if ($nature_menuiserie) {
            $this->and(\sprintf('nature_menuiserie_id = "%s"', $nature_menuiserie->id()));
        }
        if ($type_vitrage) {
            $this->and(\sprintf('type_vitrage_id = "%s" or type_vitrage_id = ""', $type_vitrage?->id()));
        }
        if ($tv_coef_transparence_ets_id) {
            $this->and(\sprintf('tv_coef_transparence_ets_id = "%s"', $tv_coef_transparence_ets_id));
        }
        return new TCollection(\array_map(fn (XMLTableElement $record) => $this->to($record), $this->getMany()));
    }

    public function to(XMLTableElement $record): T
    {
        return new T(
            id: $record->id(),
            nature_menuiserie: NatureMenuiserie::from((int) $record->nature_menuiserie),
            type_vitrage: (string) $record->type_vitrage ? TypeVitrage::from((int) $record->type_vitrage) : null,
            tv_coef_transparence_ets_id: (int) $record->tv_coef_transparence_ets_id,
            t: (float) $record->t
        );
    }
}
