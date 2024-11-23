<?php

namespace App\Database\Local\PlancherBas;

use App\Database\Local\{XMLTableElement, XMLTableRepositoryTrait};
use App\Domain\PlancherBas\Data\{Upb0, Upb0Repository};
use App\Domain\PlancherBas\Enum\TypePlancherBas;

final class XMLUpb0Repository implements Upb0Repository
{
    use XMLTableRepositoryTrait;

    public static function table(): string
    {
        return 'plancher_bas.upb0';
    }

    public function find_by(TypePlancherBas $type_plancher_bas): ?Upb0
    {
        $record = $this->createQuery()->and('type_plancher_bas', $type_plancher_bas->id())->getOne();
        return $record ? $this->to($record) : null;
    }

    protected function to(XMLTableElement $record): Upb0
    {
        return new Upb0(u0: $record->get('upb0')->floatval());
    }
}
