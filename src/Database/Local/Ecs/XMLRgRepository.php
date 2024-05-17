<?php

namespace App\Database\Local\Ecs;

use App\Database\Local\{XMLTableElement, XMLTableRepositoryTrait};
use App\Domain\Ecs\Enum\TypeGenerateur;
use App\Domain\Ecs\Table\{Rg, RgRepository};

final class XMLRgRepository implements RgRepository
{
    use XMLTableRepositoryTrait;

    public static function table(): string
    {
        return 'ecs.generateur.rg.xml';
    }

    public function find(int $id): ?Rg
    {
        return ($record = $this->createQuery()->and(\sprintf('@id = "%s"', $id))->getOne()) ? $this->to($record) : null;
    }

    public function find_by(TypeGenerateur $type_generateur): ?Rg
    {
        $record = $this->createQuery()
            ->and(\sprintf('type_generateur_id = "%s"', $type_generateur->id()))
            ->getOne();
        return $record ? $this->to($record) : null;
    }

    protected function to(XMLTableElement $record): Rg
    {
        return new Rg(
            id: $record->id(),
            rg: (float) $record->rg,
        );
    }
}
