<?php

namespace App\Domain\Enveloppe;

use App\Domain\Baie\{Baie, BaieCollection};
use App\Domain\Batiment\Batiment;
use App\Domain\Common\ValueObject\Id;
use App\Domain\Enveloppe\ValueObject\Permeabilite;
use App\Domain\Lnc\{Lnc, LncCollection};
use App\Domain\MasqueLointain\{MasqueLointain, MasqueLointainCollection};
use App\Domain\Mur\{Mur, MurCollection};
use App\Domain\Paroi\{ParoiCollection};
use App\Domain\PlancherBas\{PlancherBas, PlancherBasCollection};
use App\Domain\PlancherHaut\{PlancherHaut, PlancherHautCollection};
use App\Domain\PlancherIntermediaire\{PlancherIntermediaire, PlancherIntermediaireCollection};
use App\Domain\PontThermique\{PontThermique, PontThermiqueCollection};
use App\Domain\Porte\{Porte, PorteCollection};
use App\Domain\Refend\{Refend, RefendCollection};

final class Enveloppe
{
    public function __construct(
        private readonly Id $id,
        private readonly Batiment $batiment,
        private Permeabilite $permeabilite,
        private MasqueLointainCollection $masque_lointain_collection,
        private LncCollection $local_non_chauffe_collection,
        private BaieCollection $baie_collection,
        private MurCollection $mur_collection,
        private PlancherBasCollection $plancher_bas_collection,
        private PlancherIntermediaireCollection $plancher_intermediaire_collection,
        private PlancherHautCollection $plancher_haut_collection,
        private PorteCollection $porte_collection,
        private PontThermiqueCollection $pont_thermique_collection,
        private RefendCollection $refend_collection,
    ) {
    }

    /**
     * Créé une enveloppe
     */
    public static function create(Batiment $batiment, Permeabilite $permeabilite): self
    {
        return new self(
            id: $batiment->id(),
            batiment: $batiment,
            permeabilite: $permeabilite,
            masque_lointain_collection: new MasqueLointainCollection,
            local_non_chauffe_collection: new LncCollection,
            baie_collection: new BaieCollection,
            mur_collection: new MurCollection,
            plancher_bas_collection: new PlancherBasCollection,
            plancher_intermediaire_collection: new PlancherIntermediaireCollection,
            plancher_haut_collection: new PlancherHautCollection,
            porte_collection: new PorteCollection,
            pont_thermique_collection: new PontThermiqueCollection,
            refend_collection: new RefendCollection,
        );
    }

    public function update(Permeabilite $permeabilite): self
    {
        $this->permeabilite = $permeabilite;
        return $this;
    }

    public function id(): Id
    {
        return $this->id;
    }

    public function batiment(): Batiment
    {
        return $this->batiment;
    }

    public function permeabilite(): Permeabilite
    {
        return $this->permeabilite;
    }

    public function masque_lointain_collection(): MasqueLointainCollection
    {
        return $this->masque_lointain_collection;
    }

    public function get_masque_lointain(Id $id): ?MasqueLointain
    {
        return $this->masque_lointain_collection->find($id);
    }

    public function add_masque_lointain(MasqueLointain $entity): self
    {
        $this->masque_lointain_collection->add($entity);
        return $this;
    }

    public function remove_masque_lointain(MasqueLointain $entity): self
    {
        $this->masque_lointain_collection->removeElement($entity);
        return $this;
    }

    public function local_non_chauffe_collection(): LncCollection
    {
        return $this->local_non_chauffe_collection;
    }

    public function get_local_non_chauffe(Id $id): ?Lnc
    {
        return $this->local_non_chauffe_collection->find($id);
    }

    public function add_lnc(Lnc $entity): self
    {
        $this->local_non_chauffe_collection->add($entity);
        return $this;
    }

    public function remove_lnc(Lnc $entity): self
    {
        $this->local_non_chauffe_collection->removeElement($entity);
        return $this;
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

    public function mur_collection(): MurCollection
    {
        return $this->mur_collection;
    }

    public function get_mur(Id $id): ?Mur
    {
        return $this->mur_collection->find($id);
    }

    public function add_mur(Mur $entity): self
    {
        $this->mur_collection->add($entity);
        return $this;
    }

    public function remove_mur(Mur $entity): self
    {
        $this->mur_collection->removeElement($entity);
        return $this;
    }

    public function plancher_bas_collection(): PlancherBasCollection
    {
        return $this->plancher_bas_collection;
    }

    public function get_plancher_bas(Id $id): ?PlancherBas
    {
        return $this->plancher_bas_collection->find($id);
    }

    public function add_plancher_bas(PlancherBas $entity): self
    {
        $this->plancher_bas_collection->add($entity);
        return $this;
    }

    public function remove_plancher_bas(PlancherBas $entity): self
    {
        $this->plancher_bas_collection->removeElement($entity);
        return $this;
    }

    public function plancher_intermediaire_collection(): PlancherIntermediaireCollection
    {
        return $this->plancher_intermediaire_collection;
    }

    public function get_plancher_intermediaire(Id $id): ?PlancherIntermediaire
    {
        return $this->plancher_intermediaire_collection->find($id);
    }

    public function add_plancher_intermediaire(PlancherIntermediaire $entity): self
    {
        $this->plancher_intermediaire_collection->add($entity);
        return $this;
    }

    public function remove_plancher_intermediaire(PlancherIntermediaire $entity): self
    {
        $this->plancher_intermediaire_collection->removeElement($entity);
        return $this;
    }

    public function plancher_haut_collection(): PlancherHautCollection
    {
        return $this->plancher_haut_collection;
    }

    public function get_plancher_haut(Id $id): ?PlancherHaut
    {
        return $this->plancher_haut_collection->find($id);
    }

    public function add_plancher_haut(PlancherHaut $entity): self
    {
        $this->plancher_haut_collection->add($entity);
        return $this;
    }

    public function remove_plancher_haut(PlancherHaut $entity): self
    {
        $this->plancher_haut_collection->removeElement($entity);
        return $this;
    }

    public function porte_collection(): PorteCollection
    {
        return $this->porte_collection;
    }

    public function get_porte(Id $id): ?Porte
    {
        return $this->porte_collection->find($id);
    }

    public function add_porte(Porte $entity): self
    {
        $this->porte_collection->add($entity);
        return $this;
    }

    public function remove_porte(Porte $entity): self
    {
        $this->porte_collection->removeElement($entity);
        return $this;
    }

    public function pont_thermique_collection(): PontThermiqueCollection
    {
        return $this->pont_thermique_collection;
    }

    public function get_pont_thermique(Id $id): ?PontThermique
    {
        return $this->pont_thermique_collection->find($id);
    }

    public function add_pont_thermique(PontThermique $entity): self
    {
        $this->pont_thermique_collection->add($entity);
        return $this;
    }

    public function remove_pont_thermique(PontThermique $entity): self
    {
        $this->pont_thermique_collection->removeElement($entity);
        return $this;
    }

    public function refend_collection(): RefendCollection
    {
        return $this->refend_collection;
    }

    public function get_refend(Id $id): ?Refend
    {
        return $this->refend_collection->find($id);
    }

    public function add_refend(Refend $entity): self
    {
        $this->refend_collection->add($entity);
        return $this;
    }

    public function remove_refend(Refend $entity): self
    {
        $this->refend_collection->removeElement($entity);
        return $this;
    }

    public function paroi_collection(): ParoiCollection
    {
        return new ParoiCollection([
            ...$this->baie_collection->to_array(),
            ...$this->mur_collection->to_array(),
            ...$this->plancher_bas_collection->to_array(),
            ...$this->plancher_haut_collection->to_array(),
            ...$this->porte_collection->to_array(),
        ]);
    }
}
