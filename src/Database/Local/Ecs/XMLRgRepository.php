<?php

namespace App\Database\Local\Ecs;

use App\Domain\Ecs\Data\{Rg, RgRepository};
use App\Database\Local\{XMLTableElement, XMLTableRepositoryTrait};
use App\Domain\Ecs\Enum\{EnergieGenerateur, TypeGenerateur};

final class XMLRgRepository implements RgRepository
{
    use XMLTableRepositoryTrait;

    public static function table(): string
    {
        return 'ecs.rg';
    }

    public function find_by(TypeGenerateur $type_generateur, EnergieGenerateur $energie_generateur,): ?Rg
    {
        $record = $this->createQuery()
            ->and('type_generateur', $type_generateur->id())
            ->and('energie_generateur', $energie_generateur->id())
            ->getOne();
        return $record ? $this->to($record) : null;
    }

    protected function to(XMLTableElement $record): Rg
    {
        return new Rg(rg: (float) $record->rg,);
    }
}
