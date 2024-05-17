<?php

namespace App\Database\Local\PlancherHaut;

use App\Database\Local\{XMLTableElement, XMLTableRepositoryTrait};
use App\Domain\PlancherHaut\Enum\TypePlancherHaut;
use App\Domain\PlancherHaut\Table\{Uph0, Uph0Repository};

final class XMLUph0Repository implements Uph0Repository
{
    use XMLTableRepositoryTrait;

    public static function table(): string
    {
        return 'plancher_haut.uph0.xml';
    }

    public function find(int $id): ?Uph0
    {
        return ($record = $this->createQuery()->and(\sprintf('@id = "%s"', $id))->getOne()) ? $this->to($record) : null;
    }

    public function find_by(TypePlancherHaut $type_plancher_haut): ?Uph0
    {
        $record = $this->createQuery()->and(\sprintf('type_plancher_haut_id = "%s"', $type_plancher_haut->id()))->getOne();
        return $record ? $this->to($record) : null;
    }

    protected function to(XMLTableElement $record): Uph0
    {
        return new Uph0(
            id: $record->id(),
            type_plancher_haut: TypePlancherHaut::from((int) $record->type_plancher_haut_id),
            uph0: (float) $record->uph0,
            tv_uph0_id: (int) $record->tv_uph0_id,
        );
    }
}
