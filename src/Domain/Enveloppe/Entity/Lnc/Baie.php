<?php

namespace App\Domain\Enveloppe\Entity\Lnc;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Enveloppe\Data\Lnc\BaieData;
use App\Domain\Enveloppe\Entity\Lnc;
use App\Domain\Enveloppe\Enum\Lnc\{Materiau, TypeBaie, TypeVitrage};
use App\Domain\Enveloppe\ValueObject\Lnc\PositionBaie as Position;

final class Baie
{
    public function __construct(
        private readonly Id $id,
        private readonly Lnc $local_non_chauffe,
        private string $description,
        private TypeBaie $type,
        private ?Materiau $materiau,
        private ?TypeVitrage $type_vitrage,
        private ?bool $presence_rupteur_pont_thermique,
        private Position $position,
        private BaieData $data,
    ) {}

    public static function create(
        Id $id,
        Lnc $local_non_chauffe,
        string $description,
        TypeBaie $type,
        ?Materiau $materiau,
        ?TypeVitrage $type_vitrage,
        ?bool $presence_rupteur_pont_thermique,
        Position $position,
    ): self {
        return new self(
            id: $id,
            local_non_chauffe: $local_non_chauffe,
            description: $description,
            type: $type,
            materiau: $materiau,
            type_vitrage: $type_vitrage,
            presence_rupteur_pont_thermique: $presence_rupteur_pont_thermique,
            position: $position,
            data: BaieData::create(),
        );
    }

    public function reinitialise(): self
    {
        $this->data = BaieData::create();
        return $this;
    }

    public function calcule(BaieData $data): self
    {
        $this->data = $data;
        return $this;
    }

    public function id(): Id
    {
        return $this->id;
    }

    public function local_non_chauffe(): Lnc
    {
        return $this->local_non_chauffe;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function type(): TypeBaie
    {
        return $this->type;
    }

    public function materiau(): ?Materiau
    {
        return $this->materiau;
    }

    public function type_vitrage(): ?TypeVitrage
    {
        return $this->type_vitrage;
    }

    public function presence_rupteur_pont_thermique(): ?bool
    {
        return $this->presence_rupteur_pont_thermique;
    }

    public function position(): Position
    {
        return $this->position;
    }

    public function data(): BaieData
    {
        return $this->data;
    }
}
