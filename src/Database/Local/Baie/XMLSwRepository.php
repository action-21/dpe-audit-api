<?php

namespace App\Database\Local\Baie;

use App\Database\Local\{XMLTableElement, XMLTableRepositoryTrait};
use App\Domain\Baie\Enum\{NatureMenuiserie, TypeBaie, TypePose, TypeVitrage};
use App\Domain\Baie\Table\{Sw, SwRepository};

final class XMLSwRepository implements SwRepository
{
    use XMLTableRepositoryTrait;

    public static function table(): string
    {
        return 'baie.sw.xml';
    }

    public function find(int $id): ?Sw
    {
        return ($record = $this->createQuery()->and(\sprintf('@id = "%s"', $id))->getOne()) ? $this->to($record) : null;
    }

    public function find_by(TypeBaie $type_baie, NatureMenuiserie $nature_menuiserie, ?TypePose $type_pose, ?TypeVitrage $type_vitrage): ?Sw
    {
        $record = $this->createQuery()
            ->and(\sprintf('type_baie_id = "%s"', $type_baie->id()))
            ->and(\sprintf('nature_menuiserie_id = "%s"', $nature_menuiserie->id()))
            ->and(\sprintf('type_pose_id = "%s" or type_pose_id = ""', $type_pose?->id()))
            ->and(\sprintf('type_vitrage_id = "%s" or type_vitrage_id = ""', $type_vitrage?->id()))
            ->getOne();
        return $record ? $this->to($record) : null;
    }

    protected function to(XMLTableElement $record): Sw
    {
        return new Sw(
            id: $record->id(),
            sw: (float) $record->sw,
        );
    }
}
