<?php

namespace App\Database\Local\Lnc;

use App\Database\Local\{XMLTableElement, XMLTableRepositoryTrait};
use App\Domain\Lnc\Data\{B, BRepository};

final class XMLBRepository implements BRepository
{
    use XMLTableRepositoryTrait;

    public static function table(): string
    {
        return 'lnc.b';
    }

    public function find_by(float $uvue, float $aiu, float $aue, bool $isolation_aiu, bool $isolation_aue): ?B
    {
        $record = $this->createQuery()
            ->and('uvue', $uvue)
            ->and('isolation_aiu', $isolation_aiu)
            ->and('isolation_aue', $isolation_aue)
            ->andCompareTo('aiu_aue', $aue && $aiu ? $aiu / $aue : 0)
            ->getOne();
        return $record ? $this->to($record) : null;
    }

    public function to(XMLTableElement $record): B
    {
        return new B(b: (float) $record->b);
    }
}
