<?php

namespace App\Database\Local\Ecs;

use App\Database\Local\{XMLTableElement, XMLTableRepositoryTrait};
use App\Domain\Ecs\Enum\{BouclageReseau, TypeInstallation};
use App\Domain\Ecs\Table\{Rd, RdRepository};

final class XMLRdRepository implements RdRepository
{
    use XMLTableRepositoryTrait;

    public static function table(): string
    {
        return 'ecs.generateur.rd.xml';
    }

    public function find(int $id): ?Rd
    {
        return ($record = $this->createQuery()->and(\sprintf('@id = "%s"', $id))->getOne()) ? $this->to($record) : null;
    }

    public function find_by(
        TypeInstallation $type_installation,
        ?BouclageReseau $bouclage_reseau,
        ?bool $alimentation_contigue,
        ?bool $position_volume_habitable
    ): ?Rd {
        $record = $this->createQuery()
            ->and(\sprintf('type_installation_id = "%s"', $type_installation->id()))
            ->and(\sprintf('bouclage_reseau_id = "%s" or bouclage_reseau_id = ""', $bouclage_reseau?->id()))
            ->and(\sprintf('alimentation_contigue = "%s" or alimentation_contigue = ""', null !== $alimentation_contigue ? (int) $alimentation_contigue : null))
            ->and(\sprintf('position_volume_habitable = "%s" or position_volume_habitable = ""', null !== $position_volume_habitable ? (int) $position_volume_habitable : null))
            ->getOne();
        return $record ? $this->to($record) : null;
    }

    protected function to(XMLTableElement $record): Rd
    {
        return new Rd(
            id: $record->id(),
            rd: (float) $record->rd,
        );
    }
}
