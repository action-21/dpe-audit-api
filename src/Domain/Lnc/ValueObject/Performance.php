<?php

namespace App\Domain\Lnc\ValueObject;

use App\Domain\Common\Service\Assert;
use App\Domain\Lnc\Data\BverCollection;

final class Performance
{
    public function __construct(
        public readonly ?float $uvue,
        public readonly ?float $b,
        public readonly ?BverCollection $bvers,
    ) {}

    public static function create(float $uvue, float $b): self
    {
        Assert::positif_ou_zero($uvue);
        Assert::positif_ou_zero($b);
        return new self(uvue: $uvue, b: $b, bvers: null);
    }

    public static function create_ets(BverCollection $bvers): self
    {
        Assert::non_vide($bvers);
        return new self(uvue: null, b: null, bvers: $bvers);
    }

    public function b(bool $isolation_paroi): float
    {
        return null !== $this->b ? $this->b : $this->bvers->bver(isolation_paroi: $isolation_paroi);
    }
}
