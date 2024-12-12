<?php

namespace App\Domain\Baie\Entity;

use App\Domain\Baie\Baie;
use App\Domain\Baie\Enum\TypeMasqueProche;
use App\Domain\Common\Type\Id;
use Webmozart\Assert\Assert;

final class MasqueProche
{
    public function __construct(
        private readonly Id $id,
        private readonly Baie $baie,
        private string $description,
        private TypeMasqueProche $type_masque,
        private ?float $avancee = null,
    ) {}

    public static function create(
        Baie $baie,
        string $description,
        TypeMasqueProche $type_masque,
        ?float $avancee,
    ): self {
        Assert::nullOrGreaterThanEq($avancee, 0);

        if (\in_array($type_masque, [
            TypeMasqueProche::FOND_BALCON_OU_FOND_ET_FLANC_LOGGIAS,
            TypeMasqueProche::BALCON_OU_AUVENT
        ])) {
            Assert::notNull($avancee);
        }
        if ($type_masque === TypeMasqueProche::FOND_BALCON_OU_FOND_ET_FLANC_LOGGIAS) {
            Assert::notNull($baie->orientation());
        }
        return new self(
            id: Id::create(),
            baie: $baie,
            description: $description,
            type_masque: $type_masque,
            avancee: $avancee,
        );
    }

    public function controle(): void
    {
        Assert::nullOrGreaterThanEq($this->avancee, 0);

        if (\in_array($this->type_masque, [
            TypeMasqueProche::FOND_BALCON_OU_FOND_ET_FLANC_LOGGIAS,
            TypeMasqueProche::BALCON_OU_AUVENT
        ])) {
            Assert::notNull($this->avancee);
        }
        if ($this->type_masque === TypeMasqueProche::FOND_BALCON_OU_FOND_ET_FLANC_LOGGIAS) {
            Assert::notNull($this->baie->orientation());
        }
    }

    public function id(): Id
    {
        return $this->id;
    }

    public function baie(): Baie
    {
        return $this->baie;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function type_masque(): TypeMasqueProche
    {
        return $this->type_masque;
    }

    public function avancee(): ?float
    {
        return $this->avancee;
    }
}
