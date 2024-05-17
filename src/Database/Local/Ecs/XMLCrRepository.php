<?php

namespace App\Database\Local\Ecs;

use App\Database\Local\{XMLTableElement, XMLTableRepositoryTrait};
use App\Domain\Ecs\Enum\TypeGenerateur;
use App\Domain\Ecs\Table\{Cr, CrRepository};

final class XMLCrRepository implements CrRepository
{
    use XMLTableRepositoryTrait;

    public static function table(): string
    {
        return 'ecs.generateur.cr.xml';
    }

    public function find(int $id): ?Cr
    {
        return ($record = $this->createQuery()->and(\sprintf('@id = "%s"', $id))->getOne()) ? $this->to($record) : null;
    }

    public function find_by(TypeGenerateur $type_generateur, float $volume_stockage): ?Cr
    {
        $record = $this->createQuery()
            ->and(\sprintf('type_generateur_id = "%s"', $type_generateur->id()))
            ->andCompareTo('volume_stockage', $volume_stockage)
            ->getOne();
        return $record ? $this->to($record) : null;
    }

    protected function to(XMLTableElement $record): Cr
    {
        return new Cr(
            id: $record->id(),
            cr: (float) $record->cr,
        );
    }
}
