<?php

namespace App\Domain\Eclairage\Engine;

use App\Domain\Audit\Audit;
use App\Domain\Audit\Engine\ZoneThermique;
use App\Domain\Common\EngineRule;
use App\Domain\Common\Enum\{Energie, ScenarioUsage, Usage};
use App\Domain\Common\ValueObject\Consommations;
use App\Domain\Eclairage\Service\EclairageTableValeurRepository;

final class ConsommationEclairage extends EngineRule
{
    // Constante de puissance d'éclairage en W/m²
    public final const PUISSANCE_ECLAIRAGE = 1.4;
    // Constante de taux d'utilisation de l'éclairage
    public final const TAUX_UTILISATION = 0.9;

    private Audit $audit;

    public function __construct(private readonly EclairageTableValeurRepository $table_repository) {}

    /**
     * @see \App\Domain\Audit\Engine\ZoneThermique::surface_habitable()
     */
    public function surface_habitable(): float
    {
        return $this->audit->data()->surface_habitable;
    }

    /**
     * Consommation d'éclairage en kWh/m²
     */
    public function cecl(): float
    {
        return $this->nhecl() * self::PUISSANCE_ECLAIRAGE * $this->surface_habitable() * self::TAUX_UTILISATION / 1000;
    }

    /**
     * Nombre d'heures d'éclairage par an
     */
    public function nhecl(): float
    {
        if (null === $value = $this->table_repository->nhecl(
            zone_climatique: $this->audit->adresse()->zone_climatique,
        )) {
            throw new \DomainException('Valeur forfaitaires "nhecl" non trouvée');
        }
        return $value;
    }

    public function apply(Audit $entity): void
    {
        $this->audit = $entity;

        $entity->eclairage()->calcule($entity->eclairage()->data()->with(
            consommations: Consommations::create(
                usage: Usage::ECLAIRAGE,
                energie: Energie::ELECTRICITE,
                callback: fn(ScenarioUsage $scenario) => $this->cecl(),
            ),
        ));
    }

    public static function dependencies(): array
    {
        return [ZoneThermique::class];
    }
}
