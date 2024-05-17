<?php

namespace App\Database\Local\Enveloppe;

use App\Database\Local\{XMLTableElement, XMLTableRepositoryTrait};
use App\Domain\Batiment\Enum\TypeBatiment;
use App\Domain\Enveloppe\Table\{Q4paConv, Q4paConvRepository};

final class XMLQ4paConvRepository implements Q4paConvRepository
{
    use XMLTableRepositoryTrait;

    public static function table(): string
    {
        return 'enveloppe.q4paconv.xml';
    }

    public function find(int $id): ?Q4paConv
    {
        return ($record = $this->createQuery()->and(\sprintf('@id = "%s"', $id))->getOne()) ? $this->to($record) : null;
    }

    public function find_by(TypeBatiment $type_batiment, int $annee_construction, ?bool $presence_joints_menuiserie, ?bool $isolation_murs_plafonds): ?Q4paConv
    {
        $record = $this->createQuery()
            ->and(\sprintf('type_batiment_id = "%s"', $type_batiment->id()))
            ->and(\sprintf('presence_joints_menuiserie = "" or presence_joints_menuiserie = "%s"', null !== $presence_joints_menuiserie ? (int) $presence_joints_menuiserie : null))
            ->and(\sprintf('isolation_murs_plafonds = "" or isolation_murs_plafonds = "%s"', null !== $isolation_murs_plafonds ? (int) $isolation_murs_plafonds : null))
            ->andCompareTo('annee_construction', $annee_construction)
            ->getOne();
        return $record ? $this->to($record) : null;
    }

    protected function to(XMLTableElement $record): Q4paConv
    {
        return new Q4paConv(
            id: $record->id(),
            q4pa_conv: (float) $record->q4pa_conv,
        );
    }
}
