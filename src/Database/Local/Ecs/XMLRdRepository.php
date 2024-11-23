<?php

namespace App\Database\Local\Ecs;

use App\Domain\Ecs\Data\{Rd, RdRepository};
use App\Database\Local\{XMLTableElement, XMLTableRepositoryTrait};
use App\Domain\Ecs\Enum\BouclageReseau;

final class XMLRdRepository implements RdRepository
{
    use XMLTableRepositoryTrait;

    public static function table(): string
    {
        return 'ecs.rd';
    }

    public function find_by(
        bool $reseau_collectif,
        ?BouclageReseau $bouclage_reseau,
        ?bool $alimentation_contigue,
        ?bool $production_volume_habitable,
    ): ?Rd {
        $record = $this->createQuery()
            ->and('reseau_collectif', $reseau_collectif)
            ->and('bouclage_reseau', $bouclage_reseau?->value, true)
            ->and('alimentation_contigue', $alimentation_contigue, true)
            ->and('production_volume_habitable', $production_volume_habitable, true)
            ->getOne();
        return $record ? $this->to($record) : null;
    }

    protected function to(XMLTableElement $record): Rd
    {
        return new Rd(rd: (float) $record->rd,);
    }
}
