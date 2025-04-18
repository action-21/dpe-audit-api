<?php

namespace App\Domain\Enveloppe\Entity;

use App\Domain\Common\Enum\Orientation;
use App\Domain\Common\ValueObject\Id;
use App\Domain\Enveloppe\Data\LncData;
use App\Domain\Enveloppe\Enveloppe;
use App\Domain\Enveloppe\Entity\Lnc\{Baie, BaieCollection};
use App\Domain\Enveloppe\Entity\Lnc\{ParoiOpaque, ParoiOpaqueCollection};
use App\Domain\Enveloppe\Enum\Lnc\TypeLnc;

final class Lnc
{
    public function __construct(
        private readonly Id $id,
        private readonly Enveloppe $enveloppe,
        private string $description,
        private TypeLnc $type,
        private ParoiOpaqueCollection $parois_opaques,
        private BaieCollection $baies,
        private LncData $data,
    ) {}

    public static function create(
        Id $id,
        Enveloppe $enveloppe,
        string $description,
        TypeLnc $type,
    ): self {
        return new self(
            id: $id,
            enveloppe: $enveloppe,
            description: $description,
            type: $type,
            parois_opaques: new ParoiOpaqueCollection,
            baies: new BaieCollection,
            data: LncData::create()
        );
    }

    public function reinitialise(): self
    {
        $this->data = LncData::create();
        $this->parois_opaques->reinitialise();
        $this->baies->reinitialise();
        return $this;
    }

    public function calcule(LncData $data): self
    {
        $this->data = $data;
        return $this;
    }

    public function id(): Id
    {
        return $this->id;
    }

    public function enveloppe(): Enveloppe
    {
        return $this->enveloppe;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function type(): TypeLnc
    {
        return $this->type;
    }

    /**
     * @return BaieCollection|Baie[]
     */
    public function baies(): BaieCollection
    {
        return $this->baies;
    }

    public function add_baie(Baie $entity): self
    {
        $this->baies->add($entity);
        $this->reinitialise();
        return $this;
    }

    /**
     * @return ParoiOpaqueCollection|ParoiOpaque[]
     */
    public function parois_opaques(): ParoiOpaqueCollection
    {
        return $this->parois_opaques;
    }

    public function add_paroi_opaque(ParoiOpaque $entity): self
    {
        $this->parois_opaques->add($entity);
        $this->reinitialise();
        return $this;
    }

    /**
     * @return array|Orientation[]
     */
    public function orientations(): array
    {
        return $this->baies->orientations();
    }

    public function data(): LncData
    {
        return $this->data;
    }
}
