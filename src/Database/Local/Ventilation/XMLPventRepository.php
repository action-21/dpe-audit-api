<?php

namespace App\Database\Local\Ventilation;

use App\Database\Local\{XMLTableElement, XMLTableRepositoryTrait};
use App\Domain\Batiment\Enum\TypeBatiment;
use App\Domain\Ventilation\Enum\{TypeInstallation, TypeVentilation};
use App\Domain\Ventilation\Table\{Pvent, PventRepository};

final class XMLPventRepository implements PventRepository
{
    use XMLTableRepositoryTrait;

    public static function table(): string
    {
        return 'ventilation.pvent.xml';
    }

    public function find(int $id): ?Pvent
    {
        return ($record = $this->createQuery()->and(\sprintf('@id = "%s"', $id))->getOne()) ? $this->to($record) : null;
    }

    public function find_by(
        TypeVentilation $type_ventilation,
        ?TypeBatiment $type_batiment,
        ?TypeInstallation $type_installation,
        ?int $annee_installation
    ): ?Pvent {
        $record = $this->createQuery()
            ->and(\sprintf('type_ventilation_id = "%s"', $type_ventilation->id()))
            ->and(\sprintf('type_batiment_id = "" or type_batiment_id = "%s"', $type_batiment?->id()))
            ->and(\sprintf('type_installation_id = "" or type_installation_id = "%s"', $type_installation?->id()))
            ->andCompareTo('annee_installation', $annee_installation)
            ->getOne();

        return $record ? $this->to($record) : null;
    }

    protected function to(XMLTableElement $record): Pvent
    {
        return new Pvent(
            id: $record->id(),
            ratio_utilisation: (float) $record->ratio_utilisation,
            qvarep_conv: (string) $record->qvarep_conv ? (float) $record->qvarep_conv : null,
            pvent_moy: (string) $record->pvent_moy ? (float) $record->pvent_moy : null,
            pvent: (string) $record->pvent ? (float) $record->pvent : null,
        );
    }
}
