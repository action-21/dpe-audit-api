<?php

namespace App\Database\Local\PlancherHaut;

use App\Database\Local\{XMLTableElement, XMLTableRepositoryTrait};
use App\Domain\PlancherHaut\Data\{Uph0, Uph0Repository};
use App\Domain\PlancherHaut\Enum\TypePlancherHaut;

final class XMLUph0Repository implements Uph0Repository
{
    use XMLTableRepositoryTrait;

    public static function table(): string
    {
        return 'plancher_haut.uph0';
    }

    public function find_by(TypePlancherHaut $type_plancher_haut): ?Uph0
    {
        $record = $this->createQuery()->and('type_plancher_haut', $type_plancher_haut->id())->getOne();
        return $record ? $this->to($record) : null;
    }

    protected function to(XMLTableElement $record): Uph0
    {
        return new Uph0($record->get('uph0')->floatval());
    }
}
