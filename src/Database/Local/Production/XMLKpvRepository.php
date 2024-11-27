<?php

namespace App\Database\Local\Production;

use App\Database\Local\{XMLTableElement, XMLTableRepositoryTrait};
use App\Domain\Production\Data\{Kpv, KpvRepository};

final class XMLKpvRepository implements KpvRepository
{
    use XMLTableRepositoryTrait;

    public static function table(): string
    {
        return 'production.kpv';
    }

    public function find_by(float $orientation, float $inclinaison): ?Kpv
    {
        $record = $this->createQuery()
            ->andCompareTo('orientation_pv', $orientation)
            ->andCompareTo('inclinaison_pv', $inclinaison)
            ->getOne();
        return $record ? $this->to($record) : null;
    }

    public function to(XMLTableElement $element): Kpv
    {
        return new Kpv(kpv: $element->get('kpv')->floatval());
    }
}
