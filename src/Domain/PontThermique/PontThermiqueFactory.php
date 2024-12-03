<?php

namespace App\Domain\PontThermique;

use App\Domain\Common\Type\Id;
use App\Domain\Enveloppe\Enveloppe;
use App\Domain\PontThermique\ValueObject\Liaison;
use Webmozart\Assert\Assert;

final class PontThermiqueFactory
{
    public function build(
        Id $id,
        Enveloppe $enveloppe,
        string $description,
        float $longueur,
        Liaison $liaison,
        ?float $kpt = null,
    ): PontThermique {
        Assert::greaterThan($longueur, 0);
        Assert::greaterThan($kpt, 0);

        return new PontThermique(
            id: $id,
            enveloppe: $enveloppe,
            description: $description,
            longueur: $longueur,
            liaison: $liaison,
            kpt: $kpt,
        );
    }
}
