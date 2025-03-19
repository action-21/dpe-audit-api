<?php

namespace App\Domain\Enveloppe\Entity;

use App\Domain\Baie\{Baie, BaieCollection};
use App\Domain\Common\ValueObject\Id;
use App\Domain\Mur\{Mur, MurCollection};
use App\Domain\PlancherBas\{PlancherBas, PlancherBasCollection};
use App\Domain\PlancherHaut\{PlancherHaut, PlancherHautCollection};
use App\Domain\Porte\{Porte, PorteCollection};

/**
 * @see App\Domain\Enveloppe\Enveloppe::parois()
 */
final class Parois
{
    public function __construct(
        private MurCollection $murs,
        private PlancherBasCollection $planchers_bas,
        private PlancherHautCollection $planchers_hauts,
        private BaieCollection $baies,
        private PorteCollection $portes,
    ) {}

    public static function create(): self
    {
        return new self(
            murs: new MurCollection(),
            planchers_bas: new PlancherBasCollection(),
            planchers_hauts: new PlancherHautCollection(),
            baies: new BaieCollection(),
            portes: new PorteCollection(),
        );
    }

    public function controle(): void
    {
        $this->murs->controle();
        $this->planchers_bas->controle();
        $this->planchers_hauts->controle();
        $this->baies->controle();
        $this->portes->controle();
    }

    public function reinitialise(): void
    {
        $this->murs->reinitialise();
        $this->planchers_bas->reinitialise();
        $this->planchers_hauts->reinitialise();
        $this->baies->reinitialise();
        $this->portes->reinitialise();
    }

    public function get(Id $id): ?Paroi
    {
        /** @var Paroi[] */
        $collection = \array_merge(
            $this->murs->values(),
            $this->planchers_bas->values(),
            $this->planchers_hauts->values(),
            $this->baies->values(),
            $this->portes->values(),
        );

        foreach ($collection as $paroi) {
            if ($paroi->id()->compare($id))
                return $paroi;
        }
        return null;
    }

    public function murs(): MurCollection
    {
        return $this->murs;
    }

    public function add_mur(Mur $entity): self
    {
        $this->murs->add($entity);
        return $this;
    }

    public function remove_mur(Mur $entity): self
    {
        $this->murs->remove($entity);
        return $this;
    }

    public function planchers_bas(): PlancherBasCollection
    {
        return $this->planchers_bas;
    }

    public function add_plancher_bas(PlancherBas $entity): self
    {
        $this->planchers_bas->add($entity);
        return $this;
    }

    public function remove_plancher_bas(PlancherBas $entity): self
    {
        $this->planchers_bas->remove($entity);
        return $this;
    }

    public function planchers_hauts(): PlancherHautCollection
    {
        return $this->planchers_hauts;
    }

    public function add_plancher_haut(PlancherHaut $entity): self
    {
        $this->planchers_hauts->add($entity);
        return $this;
    }

    public function remove_plancher_haut(PlancherHaut $entity): self
    {
        $this->planchers_hauts->remove($entity);
        return $this;
    }

    public function baies(): BaieCollection
    {
        return $this->baies;
    }

    public function add_baie(Baie $entity): self
    {
        $this->baies->add($entity);
        return $this;
    }

    public function remove_baie(Baie $entity): self
    {
        $this->baies->remove($entity);
        return $this;
    }

    public function portes(): PorteCollection
    {
        return $this->portes;
    }

    public function add_porte(Porte $entity): self
    {
        $this->portes->add($entity);
        return $this;
    }

    public function remove_porte(Porte $entity): self
    {
        $this->portes->remove($entity);
        return $this;
    }

    public function surface_deperditive(): float
    {
        $surface_deperditive = $this->murs()->surface_deperditive();
        $surface_deperditive += $this->planchers_bas()->surface_deperditive();
        $surface_deperditive += $this->planchers_hauts()->surface_deperditive();
        $surface_deperditive += $this->baies()->surface_deperditive();
        $surface_deperditive += $this->portes()->surface_deperditive();
        return $surface_deperditive;
    }

    public function deperdition(): float
    {
        $deperdition = $this->murs()->dp();
        $deperdition += $this->planchers_bas()->dp();
        $deperdition += $this->planchers_hauts()->dp();
        $deperdition += $this->baies()->dp();
        $deperdition += $this->portes()->dp();
        return $deperdition;
    }

    public function aiu(Id $local_non_chauffe_id, ?bool $isolation = null): float
    {
        if (null === $isolation) {
            $aiu = $this->murs()->filter_by_local_non_chauffe(id: $local_non_chauffe_id)->surface_deperditive();
            $aiu += $this->planchers_bas()->filter_by_local_non_chauffe(id: $local_non_chauffe_id)->surface_deperditive();
            $aiu += $this->planchers_hauts()->filter_by_local_non_chauffe(id: $local_non_chauffe_id)->surface_deperditive();
            $aiu += $this->baies()->filter_by_local_non_chauffe(id: $local_non_chauffe_id)->surface_deperditive();
            $aiu += $this->portes()->filter_by_local_non_chauffe(id: $local_non_chauffe_id)->surface_deperditive();
            return $aiu;
        }
        $aiu = $this->murs()->filter_by_local_non_chauffe(id: $local_non_chauffe_id)->filter_by_isolation(isolation: $isolation)->surface_deperditive();
        $aiu += $this->planchers_bas()->filter_by_local_non_chauffe(id: $local_non_chauffe_id)->filter_by_isolation(isolation: $isolation)->surface_deperditive();
        $aiu += $this->planchers_hauts()->filter_by_local_non_chauffe(id: $local_non_chauffe_id)->filter_by_isolation(isolation: $isolation)->surface_deperditive();
        $aiu += $this->baies()->filter_by_local_non_chauffe(id: $local_non_chauffe_id)->filter_by_isolation(isolation: $isolation)->surface_deperditive();
        $aiu += $this->portes()->filter_by_local_non_chauffe(id: $local_non_chauffe_id)->filter_by_isolation(isolation: $isolation)->surface_deperditive();
        return $aiu;
    }
}
