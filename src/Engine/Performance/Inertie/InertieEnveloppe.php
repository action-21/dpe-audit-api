<?php

namespace App\Engine\Performance\Inertie;

use App\Domain\Audit\Audit;
use App\Domain\Enveloppe\Enum\Inertie;
use App\Domain\Enveloppe\Enveloppe;
use App\Engine\Performance\Rule;

final class InertieEnveloppe extends Rule
{
    private Enveloppe $enveloppe;

    /**
     * Etat d'inertie de l'enveloppe
     */
    public function inertie(): Inertie
    {
        /** @var Inertie[] */
        $inerties = [];
        /** @var float[] */
        $surfaces = [];

        foreach (Inertie::cases() as $inertie) {
            $inerties[] = $inertie;
            $surfaces[] = $this->enveloppe->niveaux()->with_inertie($inertie)->surface();
        }
        foreach ($inerties as $key => $inertie) {
            if ($surfaces[$key] < max($surfaces)) {
                unset($inerties[$key]);
                unset($surfaces[$key]);
            }
        }
        if (count($inerties) === 1) {
            return current($inerties);
        }
        if (count($inerties) === 4 || count($inerties) === 3) {
            return Inertie::MOYENNE;
        }
        if (false === in_array(Inertie::LEGERE, $inerties)) {
            return Inertie::LOURDE;
        }
        return Inertie::MOYENNE;
    }

    public function apply(Audit $entity): void
    {
        $this->enveloppe = $entity->enveloppe();

        $entity->enveloppe()->calcule($entity->enveloppe()->data()->with(
            inertie: $this->inertie(),
        ));
    }

    public static function dependencies(): array
    {
        return [InertieNiveau::class];
    }
}
