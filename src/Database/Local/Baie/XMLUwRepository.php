<?php

namespace App\Database\Local\Baie;

use App\Database\Local\{XMLTableElement, XMLTableRepositoryTrait};
use App\Domain\Baie\Data\{Uw, UwCollection, UwRepository};
use App\Domain\Baie\Enum\{TypeBaie, NatureMenuiserie};

final class XMLUwRepository implements UwRepository
{
    use XMLTableRepositoryTrait;

    public static function table(): string
    {
        return 'baie.uw';
    }

    public function search_by(
        TypeBaie $type_baie,
        ?NatureMenuiserie $nature_menuiserie,
        ?bool $presence_soubassement,
        ?bool $presence_rupteur_pont_thermique,
    ): UwCollection {
        return new UwCollection(\array_map(
            fn(XMLTableElement $record): Uw => $this->to($record),
            $this->createQuery()
                ->and('type_baie', $type_baie->id())
                ->and('nature_menuiserie', $nature_menuiserie?->id(), true)
                ->and('presence_soubassement', $presence_soubassement, true)
                ->and('presence_rupteur_pont_thermique', $presence_rupteur_pont_thermique, true)
                ->getMany(),
        ));
    }

    protected function to(XMLTableElement $record): Uw
    {
        return new Uw(
            ug: $record->get('ug')->floatval(),
            uw: $record->get('uw')->floatval(),
        );
    }
}
