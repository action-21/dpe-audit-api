<?php

namespace App\Api\Enveloppe\Model;

use App\Domain\Enveloppe\Enum\Exposition;
use App\Domain\Enveloppe\Enveloppe as Entity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @property Lnc[] $locaux_non_chauffes
 * @property Mur[] $murs
 * @property PlancherBas[] $planchers_bas
 * @property PlancherHaut[] $planchers_hauts
 * @property Baie[] $baies
 * @property Porte[] $portes
 * @property PontThermique[] $ponts_thermiques
 * @property Niveau[] $niveaux
 */
final class Enveloppe
{
    public function __construct(
        public readonly Exposition $exposition,

        #[Assert\Positive]
        public readonly ?float $q4pa_conv,

        #[Assert\All([new Assert\Type(Lnc::class)])]
        #[Assert\Valid]
        public readonly array $locaux_non_chauffes,

        #[Assert\All([new Assert\Type(Baie::class)])]
        #[Assert\Valid]
        public readonly array $baies,

        #[Assert\All([new Assert\Type(Mur::class)])]
        #[Assert\Valid]
        public readonly array $murs,

        #[Assert\All([new Assert\Type(PlancherBas::class)])]
        #[Assert\Valid]
        public readonly array $planchers_bas,

        #[Assert\All([new Assert\Type(PlancherHaut::class)])]
        #[Assert\Valid]
        public readonly array $planchers_hauts,

        #[Assert\All([new Assert\Type(Porte::class)])]
        #[Assert\Valid]
        public readonly array $portes,

        #[Assert\All([new Assert\Type(PontThermique::class)])]
        #[Assert\Valid]
        public readonly array $ponts_thermiques,

        #[Assert\All([new Assert\Type(Niveau::class)])]
        #[Assert\Valid]
        public readonly array $niveaux,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            exposition: $entity->exposition(),
            q4pa_conv: $entity->q4pa_conv(),
            locaux_non_chauffes: Lnc::from_collection($entity->locaux_non_chauffes()),
            baies: Baie::from_collection($entity->baies()),
            murs: Mur::from_collection($entity->murs()),
            planchers_bas: PlancherBas::from_collection($entity->planchers_bas()),
            planchers_hauts: PlancherHaut::from_collection($entity->planchers_hauts()),
            portes: Porte::from_collection($entity->portes()),
            ponts_thermiques: PontThermique::from_collection($entity->ponts_thermiques()),
            niveaux: Niveau::from_collection($entity->niveaux()),
        );
    }

    #[Assert\IsTrue]
    public function is_reference_pont_thermique_exists(): bool
    {
        foreach ($this->ponts_thermiques as $item) {
            if (null === array_find($this->murs, fn(Mur $paroi) => $paroi->id === $item->liaison->mur_id)) {
                return false;
            }
            if ($item->liaison->plancher_id) {
                if (array_find($this->planchers_bas, fn(PlancherBas $paroi) => $paroi->id === $item->liaison->plancher_id)) {
                    return true;
                }
                if (array_find($this->planchers_hauts, fn(PlancherHaut $paroi) => $paroi->id === $item->liaison->plancher_id)) {
                    return true;
                }
                return false;
            }
            if ($item->liaison->ouverture_id) {
                if (array_find($this->baies, fn(Baie $paroi) => $paroi->id === $item->liaison->ouverture_id)) {
                    return true;
                }
                if (array_find($this->portes, fn(Porte $paroi) => $paroi->id === $item->liaison->ouverture_id)) {
                    return true;
                }
                return false;
            }
        }
        return true;
    }

    #[Assert\IsTrue]
    public function is_reference_baie_exists(): bool
    {
        foreach ($this->baies as $baie) {
            if (null === $baie->position->paroi_id) {
                continue;
            }
            if (array_find($this->murs, fn(Mur $paroi) => $paroi->id === $baie->position->paroi_id)) {
                return true;
            }
            if (array_find($this->planchers_bas, fn(PlancherBas $paroi) => $paroi->id === $baie->position->paroi_id)) {
                return true;
            }
            if (array_find($this->planchers_hauts, fn(PlancherHaut $paroi) => $paroi->id === $baie->position->paroi_id)) {
                return true;
            }
            return false;
        }
        return true;
    }

    #[Assert\IsTrue]
    public function is_reference_porte_exists(): bool
    {
        foreach ($this->portes as $porte) {
            if (null === $porte->position->paroi_id) {
                continue;
            }
            if (array_find($this->murs, fn(Mur $paroi) => $paroi->id === $porte->position->paroi_id)) {
                return true;
            }
            if (array_find($this->planchers_bas, fn(PlancherBas $paroi) => $paroi->id === $porte->position->paroi_id)) {
                return true;
            }
            if (array_find($this->planchers_hauts, fn(PlancherHaut $paroi) => $paroi->id === $porte->position->paroi_id)) {
                return true;
            }
            return false;
        }
        return true;
    }
}
