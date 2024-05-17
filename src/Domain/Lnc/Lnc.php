<?php

namespace App\Domain\Lnc;

use App\Domain\Enveloppe\Enveloppe;
use App\Domain\Common\ValueObject\Id;
use App\Domain\Lnc\Entity\{Baie, BaieCollection, ParoiCollection, ParoiOpaque, ParoiOpaqueCollection};
use App\Domain\Lnc\Enum\TypeLnc;

/**
 * Local non chauffé
 */
final class Lnc
{
    public function __construct(
        private readonly Id $id,
        private readonly Enveloppe $enveloppe,
        private string $description,
        private TypeLnc $type_lnc,
        private BaieCollection $baie_collection,
        private ParoiOpaqueCollection $paroi_opaque_collection,
    ) {
    }

    public static function create(Enveloppe $enveloppe, string $description, TypeLnc $type_lnc): self
    {
        return new self(
            id: Id::create(),
            enveloppe: $enveloppe,
            description: $description,
            type_lnc: $type_lnc,
            baie_collection: new BaieCollection,
            paroi_opaque_collection: new ParoiOpaqueCollection,
        );
    }

    public function update(string $description, TypeLnc $type_lnc): self
    {
        $this->description = $description;
        $this->type_lnc = $type_lnc;
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

    public function description(): ?string
    {
        return $this->description;
    }

    public function isolation_aiu(): bool
    {
        return $this->enveloppe->paroi_collection()->search_by_local_non_chauffe($this)->est_isole();
    }

    public function isolation_aue(): bool
    {
        return $this->paroi_collection()->isolation();
    }

    /**
     * Somme des surfaces des parois qui donnent sur des locaux chauffés
     */
    public function surface_aiu(): float
    {
        return $this->enveloppe->paroi_collection()->search_by_local_non_chauffe($this)->surface_deperditive();
    }

    /**
     * Somme des surfaces des parois qui donnent sur l'extérieur ou en contact avec le sol (paroi enterrée, terre-plein)
     */
    public function surface_aue(): float
    {
        return $this->paroi_collection()->surface();
    }

    public function type_lnc(): TypeLnc
    {
        return $this->type_lnc;
    }

    /**
     * Espace tampon solarisé
     */
    public function ets(): bool
    {
        return $this->type_lnc() === TypeLnc::ESPACE_TAMPON_SOLARISE;
    }

    public function paroi_collection(): ParoiCollection
    {
        return new ParoiCollection([...$this->paroi_opaque_collection->values(), ...$this->baie_collection->values()]);
    }

    public function baie_collection(): BaieCollection
    {
        return $this->baie_collection;
    }

    public function get_baie(Id $id): ?Baie
    {
        return $this->baie_collection->find($id);
    }

    public function add_baie(Baie $entity): self
    {
        $this->baie_collection->add($entity);
        return $this;
    }

    public function remove_baie(Baie $entity): self
    {
        $this->baie_collection->removeElement($entity);
        return $this;
    }

    public function paroi_opaque_collection(): ParoiOpaqueCollection
    {
        return $this->paroi_opaque_collection;
    }

    public function get_paroi_opaque(Id $id): ?ParoiOpaque
    {
        return $this->paroi_opaque_collection->find($id);
    }

    public function add_paroi_opaque(ParoiOpaque $entity): self
    {
        $this->paroi_opaque_collection->add($entity);
        return $this;
    }

    public function remove_paroi_opaque(ParoiOpaque $entity): self
    {
        $this->paroi_opaque_collection->removeElement($entity);
        return $this;
    }
}
