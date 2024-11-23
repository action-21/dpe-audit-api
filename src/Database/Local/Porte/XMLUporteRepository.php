<?php

namespace App\Database\Local\Porte;

use App\Database\Local\{XMLTableElement, XMLTableRepositoryTrait};
use App\Domain\Porte\Data\{Uporte, UporteRepository};
use App\Domain\Porte\Enum\{EtatIsolation, NatureMenuiserie, TypeVitrage};

final class XMLUporteRepository implements UporteRepository
{
    use XMLTableRepositoryTrait;

    public static function table(): string
    {
        return 'porte.uporte';
    }

    public function find_by(
        bool $presence_sas,
        EtatIsolation $isolation,
        NatureMenuiserie $nature_menuiserie,
        ?TypeVitrage $type_vitrage,
        ?float $taux_vitrage
    ): ?Uporte {
        $record = $this->createQuery()
            ->and('presence_sas', $presence_sas)
            ->and('isolation', $isolation->id(), true)
            ->and('nature_menuiserie', $nature_menuiserie->id(), true)
            ->and('type_vitrage', $type_vitrage?->id(), true)
            ->andCompareTo('taux_vitrage', $taux_vitrage)
            ->getOne();
        return $record ? $this->to($record) : null;
    }

    protected function to(XMLTableElement $record): Uporte
    {
        return new Uporte(u: $record->get('uporte')->floatval());
    }
}
