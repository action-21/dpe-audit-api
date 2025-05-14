<?php

namespace App\Engine\Performance\Confort;

use App\Domain\Audit\Audit;
use App\Domain\Common\Enum\Orientation;
use App\Domain\Enveloppe\Enum\Baie\TypeFermeture;
use App\Domain\Enveloppe\Enum\{ConfortEte as Enum, EtatIsolation, Inertie, Mitoyennete};
use App\Domain\Enveloppe\Enveloppe;
use App\Engine\Performance\Inertie\InertieEnveloppe;
use App\Engine\Performance\Rule;
use App\Engine\Performance\SurfaceDeperditive\SurfaceDeperditivePlancherHaut;

final class ConfortEte extends Rule
{
    private Enveloppe $enveloppe;

    /**
     * @see \App\Engine\Performance\Inertie\InertieEnveloppe::inertie()
     */
    public function inertie_lourde(): bool
    {
        return in_array($this->enveloppe->data()->inertie, [
            Inertie::TRES_LOURDE,
            Inertie::LOURDE,
        ]);
    }

    /**
     * TODO: intégrer le cas des appartements au RDC ou étages intermédiaires
     * 
     * @see \App\Engine\Performance\SurfaceDeperditive\SurfaceDeperditivePlancherHaut::isolation()
     */
    public function planchers_hauts_isoles(): bool
    {
        return $this->get('planchers_hauts_isoles', function () {
            $surface = 0;
            $total = 0;

            foreach ($this->enveloppe->planchers_hauts() as $item) {
                if ($item->mitoyennete() !== Mitoyennete::EXTERIEUR) {
                    continue;
                }
                $total += $item->surface_reference();

                if ($item->data()->isolation === EtatIsolation::ISOLE) {
                    $surface += $item->surface_reference();
                    continue;
                }
            }
            return $surface > $total / 2;
        });
    }

    /**
     * Indicateur de présence de protections solaires
     */
    public function presence_protections_solaires(): bool
    {
        return $this->get('presence_protections_solaires', function () {
            foreach ($this->enveloppe->baies() as $item) {
                if (false === in_array($item->orientation()?->enum(), [
                    Orientation::EST,
                    Orientation::SUD,
                    Orientation::OUEST,
                ])) {
                    continue;
                }
                if ($item->presence_protection_solaire()) {
                    continue;
                }
                if ($item->type_fermeture() !== TypeFermeture::SANS_FERMETURE) {
                    continue;
                }
                return false;
            }
            return true;
        });
    }

    /**
     * Un logement est dit traversant si, pour chaque orientation, la surface des baies est inférieure à 75%
     * de la surface totale des baies.
     */
    public function logement_traversant(): bool
    {
        return $this->get('logement_traversant', function () {
            $total = $this->enveloppe->baies()->surface_reference();

            foreach (Orientation::cases() as $orientation) {
                $surface = $this->enveloppe->baies()->with_orientation($orientation)->surface_reference();

                if ($surface / $total >= 0.75) {
                    return false;
                }
            }
            return true;
        });
    }

    public function presence_brasseurs_air(): bool
    {
        return $this->enveloppe->presence_brasseurs_air();
    }

    /**
     * Confort d'été
     */
    public function confort_ete(): Enum
    {
        return $this->get('confort_ete', function () {
            if (!$this->presence_protections_solaires() && !$this->planchers_hauts_isoles()) {
                return Enum::INSUFFISANT;
            }

            $count = count(array_filter([
                $this->inertie_lourde(),
                $this->logement_traversant(),
                $this->presence_brasseurs_air()
            ]));

            return $count >= 2 ? Enum::BON : Enum::MOYEN;
        });
    }

    public function apply(Audit $entity): void
    {
        $this->enveloppe = $entity->enveloppe();

        $entity->enveloppe()->calcule($entity->enveloppe()->data()->with(
            inertie_lourde: $this->inertie_lourde(),
            planchers_hauts_isoles: $this->planchers_hauts_isoles(),
            presence_protections_solaires: $this->presence_protections_solaires(),
            logement_traversant: $this->logement_traversant(),
            presence_brasseurs_air: $this->presence_brasseurs_air(),
            confort_ete: $this->confort_ete(),
        ));
    }

    public static function dependencies(): array
    {
        return [
            InertieEnveloppe::class,
            SurfaceDeperditivePlancherHaut::class,
        ];
    }
}
