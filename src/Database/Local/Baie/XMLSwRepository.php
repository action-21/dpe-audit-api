<?php

namespace App\Database\Local\Baie;

use App\Database\Local\{XMLTableElement, XMLTableRepositoryTrait};
use App\Domain\Baie\Data\{Sw, SwRepository};
use App\Domain\Baie\Enum\{NatureMenuiserie, TypeBaie, TypePose, TypeVitrage};

final class XMLSwRepository implements SwRepository
{
    use XMLTableRepositoryTrait;

    public static function table(): string
    {
        return 'baie.sw';
    }

    public function find_by(
        TypeBaie $type_baie,
        ?bool $presence_soubassement,
        ?NatureMenuiserie $nature_menuiserie,
        ?TypeVitrage $type_vitrage,
        ?TypePose $type_pose,
    ): ?Sw {
        $record = $this->createQuery()
            ->and('type_baie', $type_baie->id())
            ->and('presence_soubassement', $presence_soubassement, true)
            ->and('nature_menuiserie', $nature_menuiserie?->id(), true)
            ->and('type_vitrage', $type_vitrage?->id(), true)
            ->and('type_pose', $type_pose?->id(), true)
            ->getOne();
        return $record ? $this->to($record) : null;
    }

    protected function to(XMLTableElement $record): Sw
    {
        return new Sw(sw: $record->get('sw')->floatval(),);
    }
}
