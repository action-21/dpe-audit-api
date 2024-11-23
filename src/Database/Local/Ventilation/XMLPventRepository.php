<?php

namespace App\Database\Local\Ventilation;

use App\Database\Local\{XMLTableElement, XMLTableRepositoryTrait};
use App\Domain\Ventilation\Data\{Pvent, PventRepository};
use App\Domain\Ventilation\Enum\{ModeExtraction, TypeSysteme};

final class XMLPventRepository implements PventRepository
{
    use XMLTableRepositoryTrait;

    public static function table(): string
    {
        return 'ventilation.pvent';
    }

    public function find_by(
        TypeSysteme $type_systeme,
        ?ModeExtraction $mode_extraction,
        ?int $annee_installation,
        ?bool $systeme_collectif,
    ): ?Pvent {
        $record = $this->createQuery()
            ->and('type_systeme', $type_systeme->value)
            ->and('mode_extraction', $mode_extraction?->value, true)
            ->and('systeme_collectif', $systeme_collectif, true)
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
