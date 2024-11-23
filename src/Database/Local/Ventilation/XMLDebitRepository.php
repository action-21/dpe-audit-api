<?php

namespace App\Database\Local\Ventilation;

use App\Database\Local\{XMLTableElement, XMLTableRepositoryTrait};
use App\Domain\Ventilation\Enum\{ModeExtraction, ModeInsufflation, TypeSysteme};
use App\Domain\Ventilation\Data\{Debit, DebitRepository};

final class XMLDebitRepository implements DebitRepository
{
    use XMLTableRepositoryTrait;

    public static function table(): string
    {
        return 'ventilation.debit';
    }

    public function find_by(
        TypeSysteme $type_systeme,
        ?ModeExtraction $mode_extraction,
        ?ModeInsufflation $mode_insufflation,
        ?bool $presence_echangeur,
        ?bool $systeme_collectif,
        ?int $annee_installation,
    ): ?Debit {
        $record = $this->createQuery()
            ->and('type_systeme', $type_systeme->id())
            ->and('mode_extraction', $mode_extraction?->id(), true)
            ->and('mode_insufflation', $mode_insufflation?->id(), true)
            ->and('presence_echangeur', $presence_echangeur, true)
            ->and('systeme_collectif', $systeme_collectif, true)
            ->andCompareTo('annee_installation_generateur', $annee_installation)
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
