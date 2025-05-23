<?php

namespace App\Engine\Performance\Apport;

use App\Domain\Audit\Audit;
use App\Domain\Enveloppe\Entity\Baie\MasqueProche;
use App\Domain\Enveloppe\Service\BaieTableValeurRepository;
use App\Engine\Performance\Rule;

final class EnsoleillementMasqueProche extends Rule
{
    private MasqueProche $masque;

    public function __construct(private readonly BaieTableValeurRepository $table_repository) {}

    public function fe1(): float
    {
        return $this->get("fe1", function () {
            if (null === $fe1 = $this->table_repository->fe1(
                type_masque_proche: $this->masque->type_masque(),
                orientation: $this->masque->baie()->position()->orientation?->enum(),
                avancee_masque: $this->masque->profondeur(),
            )) {
                throw new \DomainException('Valeur forfaitaire fe1 non trouvée');
            }
            return $fe1;
        });
    }

    public function apply(Audit $entity): void
    {
        foreach ($entity->enveloppe()->baies() as $baie) {
            foreach ($baie->masques_proches() as $masque) {
                $this->masque = $masque;
                $this->clear();

                $masque->calcule($masque->data()->with(
                    fe1: $this->fe1(),
                ));
            }
        }
    }
}
