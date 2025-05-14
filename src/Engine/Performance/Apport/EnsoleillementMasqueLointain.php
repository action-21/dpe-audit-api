<?php

namespace App\Engine\Performance\Apport;

use App\Domain\Audit\Audit;
use App\Domain\Enveloppe\Entity\Baie\MasqueLointain;
use App\Domain\Enveloppe\Enum\Baie\{SecteurChampsVision, TypeMasqueLointain};
use App\Domain\Enveloppe\Service\BaieTableValeurRepository;
use App\Engine\Performance\Rule;

final class EnsoleillementMasqueLointain extends Rule
{
    private MasqueLointain $masque;

    public function __construct(private readonly BaieTableValeurRepository $table_repository) {}

    public function secteur(): ?SecteurChampsVision
    {
        if ($this->masque->type_masque() !== TypeMasqueLointain::MASQUE_LOINTAIN_NON_HOMOGENE) {
            return null;
        }
        if (null === $orientation_baie = $this->masque->baie()->orientation()) {
            return null;
        }
        return SecteurChampsVision::determine(
            baie: $orientation_baie,
            masque: $this->masque->orientation(),
        );
    }

    /**
     * Facteur d'ensoleillement
     */
    public function fe2(): float
    {
        return $this->get("fe2", function () {
            if ($this->masque->type_masque() !== TypeMasqueLointain::MASQUE_LOINTAIN_HOMOGENE) {
                return 1;
            }
            if (null === $fe2 = $this->table_repository->fe2(
                type_masque_lointain: $this->masque->type_masque(),
                orientation: $this->masque->baie()->position()->orientation?->enum(),
                hauteur_masque_alpha: $this->masque->hauteur(),
            )) {
                throw new \DomainException('Valeur forfaitaire fe2 non trouvée');
            }
            return $fe2;
        });
    }

    /**
     * Facteur d'ombrage
     */
    public function omb(): float
    {
        return $this->get("omb", function () {
            if ($this->masque->type_masque() !== TypeMasqueLointain::MASQUE_LOINTAIN_NON_HOMOGENE) {
                return 0;
            }
            if (null === $this->secteur()) {
                return 0;
            }
            if (null === $omb = $this->table_repository->omb(
                type_masque_lointain: $this->masque->type_masque(),
                secteur: $this->secteur(),
                orientation: $this->masque->baie()->orientation()->enum(),
                hauteur_masque_alpha: $this->masque->hauteur(),
            )) {
                throw new \DomainException('Valeur forfaitaire omb non trouvée');
            }
            return $omb;
        });
    }

    public function apply(Audit $entity): void
    {
        foreach ($entity->enveloppe()->baies() as $baie) {
            foreach ($baie->masques_lointains() as $masque) {
                $this->masque = $masque;
                $this->clear();

                $masque->calcule($masque->data()->with(
                    fe2: $this->fe2(),
                    omb: $this->omb(),
                ));
            }
        }
    }
}
