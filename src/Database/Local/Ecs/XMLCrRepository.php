<?php

namespace App\Database\Local\Ecs;

use App\Domain\Ecs\Data\{Cr, CrRepository};
use App\Database\Local\{XMLTableElement, XMLTableRepositoryTrait};
use App\Domain\Ecs\Enum\{LabelGenerateur, TypeGenerateur};

final class XMLCrRepository implements CrRepository
{
    use XMLTableRepositoryTrait;

    public static function table(): string
    {
        return 'ecs.cr';
    }

    public function find_by(TypeGenerateur $type_generateur, int $volume_stockage, ?LabelGenerateur $label_generateur,): ?Cr
    {
        $record = $this->createQuery()
            ->and('type_generateur', $type_generateur->id())
            ->and('label_generateur', $label_generateur?->id(), true)
            ->andCompareTo('volume_stockage', $volume_stockage)
            ->getOne();
        return $record ? $this->to($record) : null;
    }

    protected function to(XMLTableElement $record): Cr
    {
        return new Cr(cr: (float) $record->cr,);
    }
}
