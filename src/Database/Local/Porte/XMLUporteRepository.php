<?php

namespace App\Database\Local\Porte;

use App\Database\Local\{XMLTableElement, XMLTableRepositoryTrait};
use App\Domain\Porte\Enum\{NatureMenuiserie, TypePorte};
use App\Domain\Porte\Table\{Uporte, UporteRepository};

final class XMLUporteRepository implements UporteRepository
{
    use XMLTableRepositoryTrait;

    public static function table(): string
    {
        return 'porte.uporte.xml';
    }

    public function find(int $id): ?Uporte
    {
        return ($record = $this->createQuery()->and(\sprintf('@id = "%s"', $id))->getOne()) ? $this->to($record) : null;
    }

    public function find_by(NatureMenuiserie $nature_menuiserie, TypePorte $type_porte): ?Uporte
    {
        $record = $this->createQuery()
            ->and(\sprintf('nature_menuiserie_id = "%s"', $nature_menuiserie->id()))
            ->and(\sprintf('type_porte_id = "%s"', $type_porte->id()))
            ->getOne();
        return $record ? $this->to($record) : null;
    }

    protected function to(XMLTableElement $record): Uporte
    {
        return new Uporte(
            id: $record->id(),
            uporte: (float) $record->uporte,
        );
    }
}
