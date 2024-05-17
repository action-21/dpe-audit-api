<?php

namespace App\Database\Local\Ventilation;

use App\Database\Local\{XMLTableElement, XMLTableRepositoryTrait};
use App\Domain\Ventilation\Enum\{TypeInstallation, TypeVentilation};
use App\Domain\Ventilation\Table\{Debit, DebitRepository};

final class XMLDebitRepository implements DebitRepository
{
    use XMLTableRepositoryTrait;

    public static function table(): string
    {
        return 'ventilation.debit.xml';
    }

    public function find(int $id): ?Debit
    {
        return ($record = $this->createQuery()->and(\sprintf('@id = "%s"', $id))->getOne()) ? $this->to($record) : null;
    }

    public function find_by(TypeVentilation $type_ventilation, ?TypeInstallation $type_installation, ?int $annee_installation): ?Debit
    {
        $record = $this->createQuery()
            ->and(\sprintf('type_ventilation_id = "%s"', $type_ventilation->id()))
            ->and(\sprintf('type_installation_id = "" or type_installation_id = "%s"', $type_installation?->id()))
            ->andCompareTo('annee_installation', $annee_installation)
            ->getOne();
        return $record ? $this->to($record) : null;
    }

    protected function to(XMLTableElement $record): Debit
    {
        return new Debit(
            id: $record->id(),
            qvarep_conv: (float) $record->qvarep_conv,
            qvasouf_conv: (float) $record->qvasouf_conv,
            smea_conv: (float) $record->smea_conv,
        );
    }
}
