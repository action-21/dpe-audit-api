<?php

namespace App\Database\Local\Ventilation;

use App\Database\Local\{XMLTableElement, XMLTableRepositoryTrait};
use App\Domain\Ventilation\Data\{Pvent, PventRepository};
use App\Domain\Ventilation\Enum\{TypeGenerateur, TypeVentilation, TypeVmc};

final class XMLPventRepository implements PventRepository
{
    use XMLTableRepositoryTrait;

    public static function table(): string
    {
        return 'ventilation.pvent';
    }

    public function find_by(
        TypeVentilation $type_ventilation,
        ?TypeGenerateur $type_generateur,
        ?TypeVmc $type_vmc,
        ?int $annee_installation,
        ?bool $generateur_collectif,
    ): ?Pvent {
        $record = $this->createQuery()
            ->and('type_ventilation', $type_ventilation->value)
            ->and('type_generateur', $type_generateur?->value, true)
            ->and('type_vmc', $type_vmc?->value, true)
            ->and('generateur_collectif', $generateur_collectif, true)
            ->andCompareTo('annee_installation', $annee_installation)
            ->getOne();
        return $record ? $this->to($record) : null;
    }

    protected function to(XMLTableElement $record): Pvent
    {
        return new Pvent(
            ratio_utilisation: $record->get('ratio_utilisation')->floatval(),
            pvent: $record->get('pvent')->floatval(),
            pvent_moy: $record->get('pvent_moy')->floatval(),
        );
    }
}
