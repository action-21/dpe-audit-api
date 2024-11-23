<?php

namespace App\Database\Local\Lnc;

use App\Database\Local\{XMLTableElement, XMLTableRepositoryTrait};
use App\Domain\Lnc\Enum\{NatureMenuiserie, TypeBaie, TypeVitrage};
use App\Domain\Lnc\Data\{T, TRepository};

final class XMLTRepository implements TRepository
{
    use XMLTableRepositoryTrait;

    public static function table(): string
    {
        return 'lnc.baie.t';
    }

    public function find_by(
        TypeBaie $type_baie,
        ?NatureMenuiserie $nature_menuiserie,
        ?TypeVitrage $type_vitrage,
        ?bool $presence_rupteur_pont_thermique,
    ): ?T {
        $record = $this->createQuery()
            ->and('type_baie', $type_baie->id(), true)
            ->and('nature_menuiserie', $nature_menuiserie?->id(), true)
            ->and('presence_rupteur_pont_thermique', $presence_rupteur_pont_thermique, true)
            ->and('type_vitrage', $type_vitrage?->id(), true)
            ->getOne();
        return $record ? $this->to($record) : null;
    }

    public function to(XMLTableElement $record): T
    {
        return new T(t: (float) $record->t);
    }
}
