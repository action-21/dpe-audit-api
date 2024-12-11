<?php

namespace App\Database\Local\Ventilation;

use App\Database\Local\{XMLTableElement, XMLTableRepositoryTrait};
use App\Domain\Ventilation\Enum\{TypeGenerateur, TypeVentilation, TypeVmc};
use App\Domain\Ventilation\Data\{Debit, DebitRepository};

final class XMLDebitRepository implements DebitRepository
{
    use XMLTableRepositoryTrait;

    public static function table(): string
    {
        return 'ventilation.debit';
    }

    public function find_by(
        TypeVentilation $type_ventilation,
        ?TypeGenerateur $type_generateur,
        ?TypeVmc $type_vmc,
        ?bool $presence_echangeur_thermique,
        ?bool $generateur_collectif,
        ?int $annee_installation,
    ): ?Debit {
        $record = $this->createQuery()
            ->and('type_ventilation', $type_ventilation->id())
            ->and('type_generateur', $type_generateur?->id(), true)
            ->and('type_vmc', $type_vmc?->id(), true)
            ->and('presence_echangeur_thermique', $presence_echangeur_thermique, true)
            ->and('generateur_collectif', $generateur_collectif, true)
            ->andCompareTo('annee_installation', $annee_installation)
            ->getOne();
        return $record ? $this->to($record) : null;
    }

    protected function to(XMLTableElement $record): Debit
    {
        return new Debit(
            qvarep_conv: $record->get('qvarep_conv')->floatval(),
            qvasouf_conv: $record->get('qvasouf_conv')->floatval(),
            smea_conv: $record->get('smea_conv')->floatval(),
        );
    }
}
