<?php

namespace App\Database\Local\Enveloppe;

use App\Database\Local\{XMLTableElement, XMLTableRepositoryTrait};
use App\Domain\Common\Enum\Enum;
use App\Domain\Enveloppe\Data\{Q4paConv, Q4paConvRepository};

final class XMLQ4paConvRepository implements Q4paConvRepository
{
    use XMLTableRepositoryTrait;

    public static function table(): string
    {
        return 'enveloppe.q4pa_conv';
    }

    public function find_by(
        Enum $type_batiment,
        int $annee_construction,
        ?bool $presence_joints_menuiserie,
        ?bool $isolation_murs_plafonds,
    ): ?Q4paConv {
        $record = $this->createQuery()
            ->and('type_batiment', $type_batiment->id())
            ->and('presence_joints_menuiserie', $presence_joints_menuiserie, true)
            ->and('isolation_murs_plafonds', $isolation_murs_plafonds, true)
            ->andCompareTo('annee_construction', $annee_construction)
            ->getOne();
        return $record ? $this->to($record) : null;
    }

    protected function to(XMLTableElement $record): Q4paConv
    {
        return new Q4paConv(q4pa_conv: (float) $record->q4pa_conv,);
    }
}
