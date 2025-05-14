<?php

namespace App\Domain\Enveloppe\Entity\Baie;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Enveloppe\Data\Baie\DoubleFenetreData;
use App\Domain\Enveloppe\Enum\Baie\{Materiau, TypeBaie};
use App\Domain\Enveloppe\Enum\TypePose;
use App\Domain\Enveloppe\ValueObject\Baie\{Composition, Menuiserie, Performance, Vitrage};

final class DoubleFenetre
{
    public function __construct(
        private readonly Id $id,
        private Composition $composition,
        private Performance $performance,
        private DoubleFenetreData $data,
    ) {}

    public static function create(
        Id $id,
        Composition $composition,
        ?Performance $performance,
    ): self {
        return new self(
            id: $id,
            composition: $composition,
            performance: $performance ?? Performance::create(),
            data: DoubleFenetreData::create(),
        );
    }

    public function reinitialise(): self
    {
        $this->data = DoubleFenetreData::create();
        return $this;
    }

    public function calcule(DoubleFenetreData $data): self
    {
        $this->data = $data;
        return $this;
    }

    public function id(): Id
    {
        return $this->id;
    }

    public function type_baie(): TypeBaie
    {
        return $this->composition->type_baie;
    }

    public function type_pose(): ?TypePose
    {
        return $this->composition->type_pose;
    }

    public function materiau(): ?Materiau
    {
        return $this->composition->materiau;
    }

    public function presence_soubassement(): ?bool
    {
        return $this->composition->presence_soubassement;
    }

    public function vitrage(): ?Vitrage
    {
        return $this->composition->vitrage;
    }

    public function menuiserie(): ?Menuiserie
    {
        return $this->composition->menuiserie;
    }

    public function performance(): Performance
    {
        return $this->performance;
    }

    public function data(): DoubleFenetreData
    {
        return $this->data;
    }
}
