<?php

namespace App\Database\Local\Lnc;

use App\Database\Local\{XMLTableElement, XMLTableRepositoryTrait};
use App\Domain\Lnc\Table\{B, BRepository};

final class XMLBRepository implements BRepository
{
    use XMLTableRepositoryTrait;

    public static function table(): string
    {
        return 'lnc.b.xml';
    }

    public function find(int $id): ?B
    {
        return ($record = $this->createQuery()->and(\sprintf('@id = "%s"', $id))->getOne()) ? $this->to($record) : null;
    }

    public function find_by(float $uvue, ?bool $isolation_aiu, ?bool $isolation_aue, ?float $surface_aiu, ?float $surface_aue): ?B
    {
        $record = $this->createQuery()
            ->and(\sprintf('uvue = "%s"', $uvue))
            ->and(\sprintf('isolation_aiu = "" or isolation_aiu = "%s"', null === $isolation_aiu ? '' : (int) $isolation_aiu))
            ->and(\sprintf('isolation_aue = "" or isolation_aue = "%s"', null === $isolation_aue ? '' : (int) $isolation_aue))
            ->andCompareTo('aiu_aue', $surface_aue && $surface_aiu ? $surface_aiu / $surface_aue : null)
            ->getOne();
        return $record ? $this->to($record) : null;
    }

    public function to(XMLTableElement $record): B
    {
        return new B(id: $record->id(), b: (float) $record->b);
    }
}
