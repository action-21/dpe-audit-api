<?php

namespace App\Database\Local\PlancherBas;

use App\Database\Local\{XMLTableElement, XMLTableRepositoryTrait};
use App\Domain\PlancherBas\Enum\TypePlancherBas;
use App\Domain\PlancherBas\Table\{Upb0, Upb0Repository};

final class XMLUpb0Repository implements Upb0Repository
{
    use XMLTableRepositoryTrait;

    public static function table(): string
    {
        return 'plancher_bas.upb0.xml';
    }

    public function find(int $id): ?Upb0
    {
        return ($record = $this->createQuery()->and(\sprintf('@id = "%s"', $id))->getOne()) ? $this->to($record) : null;
    }

    public function find_by(TypePlancherBas $type_plancher_bas): ?Upb0
    {
        $record = $this->createQuery()->and(\sprintf('type_plancher_bas_id = "%s"', $type_plancher_bas->id()))->getOne();
        return $record ? $this->to($record) : null;
    }

    protected function to(XMLTableElement $record): Upb0
    {
        return new Upb0(
            id: $record->id(),
            upb0: (float) $record->upb0,
        );
    }
}
