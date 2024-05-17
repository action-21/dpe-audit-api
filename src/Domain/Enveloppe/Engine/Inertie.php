<?php

namespace App\Domain\Enveloppe\Engine;

use App\Domain\Enveloppe\Enum\ClasseInertie;
use App\Domain\Enveloppe\{Enveloppe, EnveloppeEngine};

/**
 * @see §7 - Détermination de l'inertie
 */
final class Inertie
{
    private Enveloppe $input;
    private EnveloppeEngine $engine;

    /**
     * Classe d'inertie du bâtiment
     */
    public function classe_inertie(): ClasseInertie
    {
        return ClasseInertie::from_inertie_parois(
            plancher_bas_lourd: $this->plancher_bas_lourd(),
            plancher_haut_lourd: $this->plancher_haut_lourd(),
            paroi_verticale_lourde: $this->paroi_verticale_lourde(),
        );
    }

    public function mur_lourd(): bool
    {
        $surface = $this->input->mur_collection()->surface_deperditive();
        $surface_lourde = $this->input->mur_collection()->search_paroi_lourde()->surface_deperditive();
        return $surface && $surface_lourde / $surface > 0.5 ? true : false;
    }

    /**
     * TODO: Prise en compte des refends
     */
    public function paroi_verticale_lourde(): bool
    {
        $surface = $this->input->mur_collection()->surface_deperditive();
        $surface_lourde = $this->input->mur_collection()->search_paroi_lourde()->surface_deperditive();
        return $surface && $surface_lourde / $surface > 0.5 ? true : false;
    }

    /**
     * TODO: Prise en compte des planchers intermédiaires
     */
    public function plancher_bas_lourd(): bool
    {
        $surface = $this->input->plancher_bas_collection()->surface_deperditive();
        $surface_lourde = $this->input->plancher_bas_collection()->search_paroi_lourde()->surface_deperditive();
        return $surface && $surface_lourde / $surface > 0.5 ? true : false;
    }

    /**
     * TODO: Prise en compte des planchers intermédiaires
     */
    public function plancher_haut_lourd(): bool
    {
        $surface = $this->input->plancher_haut_collection()->surface_deperditive();
        $surface_lourde = $this->input->plancher_haut_collection()->search_paroi_lourde()->surface_deperditive();
        return $surface && $surface_lourde / $surface > 0.5 ? true : false;
    }

    public function input(): Enveloppe
    {
        return $this->input;
    }

    public function engine(): EnveloppeEngine
    {
        return $this->engine;
    }

    public function __invoke(Enveloppe $input, EnveloppeEngine $engine): self
    {
        $service = clone $this;
        $service->input = $input;
        $service->engine = $engine;
        return $service;
    }
}
