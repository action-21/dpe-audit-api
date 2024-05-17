<?php

namespace App\Domain\Photovoltaique\Engine;

use App\Domain\Common\Enum\Mois;
use App\Domain\Common\Error\EngineTableError;
use App\Domain\Photovoltaique\Entity\PanneauPhotovoltaique;
use App\Domain\Photovoltaique\InstallationPhotovoltaiqueEngine;
use App\Domain\Photovoltaique\Table\{K, KRepository};

final class PanneauPhotovoltaiqueEngine
{
    private PanneauPhotovoltaique $input;
    private InstallationPhotovoltaiqueEngine $engine;

    private ?K $table_k;

    /**
     * Rendement moyen des modules en %
     */
    final public const RENDEMENT = 17;

    /**
     * Coefficient de perte
     */
    final public const COEFFICIENT_PERTE = 0.86;

    /**
     * Surface moyenne des capteurs par module en m²
     */
    final public const SURFACE_CAPTEURS = 1.6;

    public function __construct(private KRepository $table_k_repository)
    {
    }

    /**
     * Production d'électricité photovoltaïque annuelle en kWh
     */
    public function ppv(): float
    {
        return \array_reduce(Mois::cases(), fn (float $carry, Mois $mois): float => $carry + $this->ppv_j($mois), 0);
    }

    /**
     * Production d'électricité photovoltaïque pour le mois j en kWh
     */
    public function ppv_j(Mois $mois): float
    {
        return $this->k() * $this->scapteurs() * self::RENDEMENT / 100 * $this->epv_j($mois) * self::COEFFICIENT_PERTE;
    }

    /**
     * Surface des capteurs en m²
     */
    public function scapteurs(): float
    {
        return $this->surface_capteurs_saisi() ?? self::SURFACE_CAPTEURS * $this->nombre_modules();
    }

    /**
     * Coefficent de pondération prenant en compte l'altération par rapport à l'orientation optimale (30° au Sud)
     */
    public function k(): float
    {
        return $this->table_k()->k;
    }

    /**
     * Valeur de la table photovoltaïque . k
     */
    public function table_k(): K
    {
        if (null === $this->table_k) {
            throw new EngineTableError('photovoltaïque . k');
        }
        return $this->table_k;
    }

    public function fetch(): void
    {
        $this->table_k = $this->inclinaison() && $this->orientation()
            ? $this->table_k_repository->find_by(
                inclinaison: $this->inclinaison(),
                orientation: $this->orientation(),
            )
            : null;
    }

    // * Données d'entrée

    public function inclinaison(): ?float
    {
        return $this->input->inclinaison()?->valeur();
    }

    public function orientation(): ?float
    {
        return $this->input->orientation()?->valeur();
    }

    public function nombre_modules(): ?int
    {
        return $this->input->modules()?->valeur();
    }

    public function surface_capteurs_saisi(): ?float
    {
        return $this->input->surface_capteurs()?->valeur();
    }

    /**
     * @see \App\Batiment\Engine\Situation
     */
    public function epv_j(Mois $mois): float
    {
        return $this->engine->context()->batiment_engine()->situation()->epv_j($mois);
    }

    public function input(): PanneauPhotovoltaique
    {
        return $this->input;
    }

    public function context(): InstallationPhotovoltaiqueEngine
    {
        return $this->engine;
    }

    public function __invoke(PanneauPhotovoltaique $input, InstallationPhotovoltaiqueEngine $engine): self
    {
        $service = clone $this;
        $service->input = $input;
        $service->engine = $engine;
        $service->fetch();
        return $service;
    }
}
