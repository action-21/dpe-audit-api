<?php

namespace App\Domain\Enveloppe\Engine\Deperdition;

use App\Domain\Audit\Audit;
use App\Domain\Common\EngineRule;
use App\Domain\Enveloppe\Enum\Performance;
use App\Domain\Enveloppe\Enveloppe;

final class DeperditionEnveloppe extends EngineRule
{
    private Enveloppe $enveloppe;

    /**
     * @see \App\Domain\Enveloppe\Engine\Deperdition\DeperditionParois
     * @see \App\Domain\Enveloppe\Engine\Deperdition\DeperditionRenouvellementAir
     * @see \App\Domain\Enveloppe\Engine\Deperdition\DeperditionPontThermique
     */
    public function gv(): float
    {
        return $this->enveloppe->data()->deperditions->get();
    }

    /**
     * @see \App\Domain\Enveloppe\Engine\Deperdition\DeperditionParois
     */
    public function sdep(): float
    {
        return $this->enveloppe->data()->surfaces_deperditives->get();
    }

    /**
     * Coefficient de transmission thermique de l'enveloppe exprimé en W/K.m²
     */
    public function ubat(): float
    {
        return $this->gv() / $this->sdep();
    }

    /**
     * Indicateur de performance de l'enveloppe
     */
    public function performance(): Performance
    {
        $ubat = $this->ubat();

        return match (true) {
            $ubat > 0.85 => Performance::INSUFFISANTE,
            $ubat > 0.65 => Performance::MOYENNE,
            $ubat > 0.45 => Performance::BONNE,
            $ubat <= 0.45 => Performance::TRES_BONNE,
        };
    }

    public function apply(Audit $entity): void
    {
        $this->enveloppe = $entity->enveloppe();
        $entity->enveloppe()->calcule($entity->enveloppe()->data()->with(
            ubat: $this->ubat(),
            performance: $this->performance(),
        ));
    }

    public static function dependencies(): array
    {
        return [
            DeperditionParois::class,
            DeperditionPontThermique::class,
            DeperditionRenouvellementAir::class,
        ];
    }
}
